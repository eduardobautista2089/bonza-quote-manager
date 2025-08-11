#!/usr/bin/env bash

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=$4
WP_VERSION=${5-latest}

WP_TESTS_DIR=${WP_TESTS_DIR-/tmp/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-/tmp/wordpress}

download() {
    if [ $(which curl) ]; then
        curl -sSL "$1" -o "$2"
    elif [ $(which wget) ]; then
        wget -q "$1" -O "$2"
    fi
}

if [ "$WP_VERSION" == "latest" ]; then
    WP_VERSION=$(curl -s https://api.wordpress.org/core/version-check/1.7/ | grep -oP '"version":"\K[0-9.]+' | head -1)
fi

set -ex

# Download WordPress core
mkdir -p "$WP_CORE_DIR"
download https://wordpress.org/wordpress-$WP_VERSION.tar.gz /tmp/wordpress.tar.gz
tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C "$WP_CORE_DIR"

# Download test suite
mkdir -p "$WP_TESTS_DIR"
download https://develop.svn.wordpress.org/tags/$WP_VERSION/tests/phpunit/includes/ /tmp/includes.zip
download https://develop.svn.wordpress.org/tags/$WP_VERSION/tests/phpunit/data/ /tmp/data.zip
unzip -q /tmp/includes.zip -d "$WP_TESTS_DIR"
unzip -q /tmp/data.zip -d "$WP_TESTS_DIR"

# Create wp-tests-config.php
download https://develop.svn.wordpress.org/tags/$WP_VERSION/wp-tests-config-sample.php "$WP_TESTS_DIR"/wp-tests-config.php
sed -i "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR"/wp-tests-config.php
sed -i "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR"/wp-tests-config.php
sed -i "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR"/wp-tests-config.php
sed -i "s|localhost|$DB_HOST|" "$WP_TESTS_DIR"/wp-tests-config.php

# Drop & create database
mysql -u"$DB_USER" -p"$DB_PASS" -h"$DB_HOST" -e "DROP DATABASE IF EXISTS $DB_NAME;"
mysqladmin create "$D
