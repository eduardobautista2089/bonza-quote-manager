#!/usr/bin/env bash
#
# Usage: install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]
#
# Example:
#   ./bin/install-wp-tests.sh wordpress_test root root localhost latest
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
    WP_TAG=$(curl -s https://api.github.com/repos/WordPress/wordpress-develop/tags \
      | grep name | head -1 | awk -F '"' '{print $4}')
  else
    WP_TAG="$WP_VERSION"
  fi

  download "https://github.com/WordPress/wordpress-develop/archive/refs/tags/$WP_TAG.zip" /tmp/wp-develop.zip
  unzip -q /tmp/wp-develop.zip -d /tmp/wp-develop

  # Find extracted folder
  DEV_DIR=$(find /tmp/wp-develop -maxdepth 1 -type d -name "wordpress-develop*" | head -1)

  if [ ! -d "$DEV_DIR/tests/phpunit" ]; then
    echo "ERROR: PHPUnit test suite not found in $DEV_DIR"
    exit 1
  fi

  # Copy test suite to WP_TESTS_DIR
  cp -r "$DEV_DIR/tests/phpunit/"* "$WP_TESTS_DIR"

  # Copy wp-tests-config-sample.php from repo root
  if [ -f "$DEV_DIR/wp-tests-config-sample.php" ]; then
    cp "$DEV_DIR/wp-tests-config-sample.php" "$WP_TESTS_DIR/wp-tests-config.php"
  else
    echo "ERROR: wp-tests-config-sample.php not found in $DEV_DIR"
    exit 1
  fi

  # Set DB credentials in config
  sed -i "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR/wp-tests-config.php"
  sed -i "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR/wp-tests-config.php"
  sed -i "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR/wp-tests-config.php"
  sed -i "s|localhost|$DB_HOST|" "$WP_TESTS_DIR/wp-tests-config.php"
}

install_db() {
  # Try to drop existing DB first
  mysqladmin drop "$DB_NAME" -f --user="$DB_USER" --password="$DB_PASS" --host="$DB_HOST" || true
  # Create a fresh DB
  mysqladmin create "$DB_NAME" --user="$DB_USER" --password="$DB_PASS" --host="$DB_HOST"
}

install_wp
install_test_suite
install_db

echo "✅ WordPress and PHPUnit test suite installed successfully."
