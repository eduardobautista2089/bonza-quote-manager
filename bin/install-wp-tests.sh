#!/usr/bin/env bash

# Usage: ./install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]
if [ $# -lt 3 ]; then
    echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version]"
    exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}

set -ex

WP_TESTS_DIR=${WP_TESTS_DIR-/tmp/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-/tmp/wordpress}

download() {
    if command -v curl >/dev/null 2>&1; then
        curl -s "$1" > "$2"
    elif command -v wget >/dev/null 2>&1; then
        wget -nv -O "$2" "$1"
    fi
}

install_wp() {
    if [ -d "$WP_CORE_DIR" ]; then
        return
    fi

    mkdir -p "$WP_CORE_DIR"

    if [ "$WP_VERSION" == "latest" ]; then
        WP_VERSION=$(curl -s https://api.wordpress.org/core/version-check/1.7/ | grep -o '"version":"[0-9.]*"' | head -1 | sed 's/"version":"//;s/"//')
    fi

    ARCHIVE_NAME="wordpress-$WP_VERSION"
    download "https://wordpress.org/${ARCHIVE_NAME}.tar.gz" /tmp/wordpress.tar.gz
    tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C "$WP_CORE_DIR"
}

install_test_suite() {
    if [ -d "$WP_TESTS_DIR" ]; then
        return
    fi

    mkdir -p "$WP_TESTS_DIR"

    if command -v svn >/dev/null 2>&1; then
        echo "Using SVN to fetch WordPress test suite..."
        svn co --quiet "https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/" "$WP_TESTS_DIR/includes"
        svn co --quiet "https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/data/" "$WP_TESTS_DIR/data"
    else
        echo "SVN not found — using Git fallback..."
        git clone --depth=1 --branch "${WP_VERSION}" https://github.com/WordPress/wordpress-develop.git /tmp/wordpress-develop
        cp -r /tmp/wordpress-develop/tests/phpunit/includes "$WP_TESTS_DIR/includes"
        cp -r /tmp/wordpress-develop/tests/phpunit/data "$WP_TESTS_DIR/data"
        rm -rf /tmp/wordpress-develop
    fi
}

install_db() {
    mysqladmin create "$DB_NAME" --user="$DB_USER" --password="$DB_PASS" --host="$DB_HOST" --silent || true
}

install_wp
install_test_suite
install_db