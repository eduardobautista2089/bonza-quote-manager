#!/usr/bin/env bash
#
# Usage: install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]
#
# Example: ./bin/install-wp-tests.sh wordpress_test root root localhost latest
#

set -e

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}

WP_CORE_DIR=/tmp/wordpress
WP_TESTS_DIR=/tmp/wordpress-tests-lib

download() {
  local url=$1
  local target=$2
  echo "Downloading $url ..."
  curl -sSL "$url" -o "$target"
}

install_wp() {
  if [ -d "$WP_CORE_DIR/src" ]; then
    echo "WordPress core already installed."
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
  if [ -d "$WP_TESTS_DIR" ]; then
    echo "Test suite already installed."
    return
  fi

  mkdir -p "$WP_TESTS_DIR"

  if [ "$WP_VERSION" == "latest" ]; then
    WP_TAG=$(curl -s https://api.github.com/repos/WordPress/wordpress-develop/tags | grep name | head -1 | awk -F '"' '{print $4}')
  else
    WP_TAG="$WP_VERSION"
  fi

  download "https://github.com/WordPress/wordpress-develop/archive