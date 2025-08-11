#!/usr/bin/env bash

# Exit on error
set -e

DB_NAME=${1:-wordpress_test}
DB_USER=${2:-root}
DB_PASS=${3:-root}
DB_HOST=${4:-127.0.0.1}
WP_VERSION=${5:-latest}
WP_CORE_DIR=${WP_CORE_DIR:-/tmp/wordpress}
WP_TESTS_DIR=${WP_TESTS_DIR:-/tmp/wordpress-tests-lib}

download() {
    local url=$1
    local target=$2
    echo "Downloading $url ..."
    curl -sSL "$url" -o "$target"
}

install_wp() {
    if [ -d "$WP_CORE_DIR" ]; then
        echo "WordPress core already installed in $WP_CORE_DIR"
        return
    fi

    mkdir -p "$WP_CORE_DIR"

    if [ "$WP_VERSION" == "latest" ]; then
        ARCHIVE_URL="https://wordpress.org/latest.tar.gz"
    else
        ARCHIVE_URL="https://wordpress.org/wordpress-$WP_VERSION.tar.gz"
    fi

    download "$ARCHIVE_URL" /tmp/wordpress.tar.gz
    tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C "$WP_CORE_DIR"
}

install_test_suite() {
    mkdir -p "$WP_TESTS_DIR"

    if [ "$WP_VERSION" == "latest" ]; then
        WP_TAG=$(curl -s https://api.github.com/repos/WordPress/wordpress-develop/tags | grep 'name' | head -1 | awk -F '"' '{print $4}')
    else
        WP_TAG="$WP_VERSION"
    fi

    download "https://github.com/WordPress/wordpress-develop/archive/$WP_TAG.zip" /tmp/wp-develop.zip
    unzip -q /tmp/wp-develop.zip -d /tmp/wp-develop

    WP_DEV_DIR="/tmp/wp-develop/wordpress-develop-$WP_TAG"

    # Copy the PHPUnit test suite
    cp -r "$WP_DEV_DIR/tests/phpunit/"* "$WP_TESTS_DIR"

    # Copy the sample config from root of repo
    cp "$WP_DEV_DIR/wp-tests-config-sample.php" "$WP_TESTS_DIR/wp-tests-config.php"

    # Configure DB credentials
    sed -i "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s|localhost|$DB_HOST|" "$WP_TESTS_DIR/wp-tests-config.php"
}

install_db() {
    # Parse DB_HOST for port/socket
    PARTS=(${DB_HOST//:/ })
    DB_HOSTNAME=${PARTS[0]}
    DB_SOCK_OR_PORT=${PARTS[1]}
    EXTRA=""

    if ! [ -z "$DB_HOSTNAME" ]; then
        if [[ "$DB_SOCK_OR_PORT" =~ ^[0-9]+$ ]]; then
            EXTRA="--host=$DB_HOSTNAME --port=$DB_SOCK_OR_PORT --protocol=tcp"
        elif [ -n "$DB_SOCK_OR_PORT" ]; then
            EXTRA="--socket=$DB_SOCK_OR_PORT"
        else
            EXTRA="--host=$DB_HOSTNAME --protocol=tcp"
        fi
    fi

    # Create database if it doesn't exist
    mysqladmin create "$DB_NAME" --user="$DB_USER" --password="$DB_PASS" $EXTRA || true
}

install_wp
install_test_suite
install_db

echo "✅ WordPress test suite installed successfully."