#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Script de Instalação e Execução — Biblioteca Digital (Laravel 12)
# Alvo: Rocky Linux 8 / 9 (instalação limpa, sem nada pré-instalado)
# Uso:  chmod +x instalar_e_executar_rocky.sh && sudo ./instalar_e_executar_rocky.sh
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_PORT="${APP_PORT:-8000}"

DB_NAME="biblioteca_digital"
DB_USER="biblioteca"
DB_PASS="senha123"

if [[ "${EUID}" -ne 0 ]]; then
    echo "[ERRO] Execute este script como root ou com sudo:"
    echo "       sudo $0"
    exit 1
fi

# Usuário real (para não deixar vendor/ e .env pertencendo apenas ao root)
TARGET_USER="${SUDO_USER:-root}"

run_as_user() {
    if [[ "${TARGET_USER}" != "root" ]]; then
        sudo -u "${TARGET_USER}" env COMPOSER_ALLOW_SUPERUSER=1 "$@"
    else
        COMPOSER_ALLOW_SUPERUSER=1 "$@"
    fi
}

echo "=================================================================="
echo " 1/6 - Detectando versão do Rocky Linux"
echo "=================================================================="
if [[ ! -f /etc/os-release ]]; then
    echo "[ERRO] Arquivo /etc/os-release não encontrado."
    exit 1
fi

source /etc/os-release
EL_MAJOR="${VERSION_ID%%.*}"
echo "Sistema detectado: ${PRETTY_NAME:-Rocky Linux} (EL${EL_MAJOR})"

if [[ "${EL_MAJOR}" != "8" && "${EL_MAJOR}" != "9" ]]; then
    echo "[AVISO] Versão EL${EL_MAJOR} não testada explicitamente (esperado Rocky Linux 8 ou 9)."
fi

echo "=================================================================="
echo " 2/6 - Atualizando sistema e configurando repositórios (EPEL + Remi)"
echo "=================================================================="
dnf install -y dnf-utils epel-release curl git unzip

if ! rpm -q remi-release >/dev/null 2>&1; then
    dnf install -y "https://rpms.remirepo.net/enterprise/remi-release-${EL_MAJOR}.rpm"
fi

echo "=================================================================="
echo " 3/6 - Instalando PHP 8.3, extensões do Laravel e MariaDB"
echo "=================================================================="
dnf module reset php -y || true
dnf module enable php:remi-8.3 -y

dnf install -y \
    php \
    php-cli \
    php-common \
    php-mysqlnd \
    php-mbstring \
    php-xml \
    php-curl \
    php-zip \
    php-intl \
    php-bcmath \
    php-opcache \
    mariadb-server \
    mariadb

php -v

echo "=================================================================="
echo " 4/6 - Instalando Composer"
echo "=================================================================="
if ! command -v composer >/dev/null 2>&1; then
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")"

    if [[ "${EXPECTED_CHECKSUM}" != "${ACTUAL_CHECKSUM}" ]]; then
        echo "[ERRO] Checksum inválido do instalador do Composer."
        rm -f /tmp/composer-setup.php
        exit 1
    fi

    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm -f /tmp/composer-setup.php
fi

composer --version

echo "=================================================================="
echo " 5/6 - Iniciando MariaDB e criando banco de dados"
echo "=================================================================="
systemctl enable --now mariadb

mariadb -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL

echo "=================================================================="
echo " 6/6 - Configurando projeto Laravel (.env, vendor, key e migrations)"
echo "=================================================================="
cd "${SCRIPT_DIR}"

if [[ ! -f .env ]]; then
    run_as_user cp .env.example .env
fi

sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" .env

run_as_user composer install --no-interaction --prefer-dist

if grep -qE '^APP_KEY=$' .env; then
    run_as_user php artisan key:generate --force
fi

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R "${TARGET_USER}:${TARGET_USER}" storage bootstrap/cache vendor .env
chmod -R 775 storage bootstrap/cache

run_as_user php artisan migrate --force

# Libera a porta no firewalld, caso esteja ativo (útil em VMs)
if systemctl is-active --quiet firewalld; then
    firewall-cmd --permanent --add-port="${APP_PORT}/tcp" >/dev/null 2>&1 || true
    firewall-cmd --reload >/dev/null 2>&1 || true
fi

echo "=================================================================="
echo " Tudo pronto! Iniciando servidor Laravel na porta ${APP_PORT}..."
echo " Acesse no navegador: http://127.0.0.1:${APP_PORT}"
echo " Pressione Ctrl+C para encerrar o servidor."
echo "=================================================================="

if [[ "${TARGET_USER}" != "root" ]]; then
    exec sudo -u "${TARGET_USER}" php artisan serve --host=0.0.0.0 --port="${APP_PORT}"
else
    exec php artisan serve --host=0.0.0.0 --port="${APP_PORT}"
fi
