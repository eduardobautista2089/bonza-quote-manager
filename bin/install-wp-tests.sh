#!/usr/bin/env bash

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=$4
WP_VERSION=$5

WP_TESTS_DIR=${WP_TESTS_DIR-/tmp/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-/tmp/wordpress}

download() {
    if command -v curl >/dev/null; then
        curl -s "$1" > "$2"
    elif command -v wget >/dev/null; then
        wget -nv -O "$2" "$1"
    fi
}

install_wp() {
    if [ -d $WP_CORE_DIR ]; then
        return;
    fi

    mkdir -p $WP_CORE_DIR

    if [ "$WP_VERSION" == "latest" ]; then
        local ARCHIVE_URL="https://wordpress.org/latest.tar.gz"
    else
        local ARCHIVE_URL="https://wordpress.org/wordpress-$WP_VERSION.tar.gz"
    fi

    download $ARCHIVE_URL /tmp/wordpress.tar.gz
    tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C $WP_CORE_DIR
}

install_test_suite() {
    if [ -d $WP_TESTS_DIR ]; then
        return;
    fi

    mkdir -p $WP_TESTS_DIR
    svn co --quiet https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/ $WP_TESTS_DIR/includes
    svn co --quiet https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/data/ $WP_TESTS_DIR/data
}

install_db() {
    mysqladmin create $DB_NAME --user="$DB_USER" --password="$DB_PASS" --host="$DB_HOST" --silent
}

install_wp
install_test_suite
install_db