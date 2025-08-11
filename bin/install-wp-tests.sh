#!/usr/bin/env bash
#
# Usage: bin/install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]
#
# Example: bin/install-wp-tests.sh wordpress_test root root 127.0.0.1 latest
#
# This script will:
# 1. Download WordPress core
# 2. Download the PHPUnit test suite from the WordPress GitHub mirror
# 3. Create a wp-tests-config.php file
# 4. Create the database for testing

set -ex

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}

WP_CORE_DIR=/tmp/wordpress
WP_TESTS_DIR=/tmp/wordpress-tests-lib

# Download helper
download() {
    local url=$1
    local target=$2
    echo "Downloading $url ..."
    curl -sSL "$url" -o "$target"
}

install_wp() {
    if [ -d "$WP_CORE_DIR" ]; then
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
        # Get latest tag from GitHub API
        WP_TAG=$(curl -s https://api.github.com/repos/WordPress/wordpress-develop/tags | grep 'name' | head -1 | awk -F '"' '{print $4}')
    else
        WP_TAG="{$WP_VERSION}"
    fi

    # Download develop repo from GitHub
    download "https://github.com/WordPress/wordpress-develop/archive/$WP_TAG.zip" /tmp/wp-develop.zip
    unzip -q /tmp/wp-develop.zip -d /tmp/wp-develop

    # Copy test suite
    cp -r /tmp/wp-develop/wordpress-develop-$WP_TAG/tests/phpunit/* "$WP_TESTS_DIR"

    # Copy sample config
    cp "$WP_TESTS_DIR/wp-tests-config-sample.php" "$WP_TESTS_DIR/wp-tests-config.php"

    # Update config
    sed -i "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s|localhost|$DB_HOST|" "$WP_TESTS_DIR/wp-tests-config.php"
}

install_db() {
    mysqladmin create "$DB_NAME" --user="$DB_USER" --password="$DB_PASS" --host="$DB_HOST" || true
}

install_wp
install_test_suite
install_db
