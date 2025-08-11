#!/usr/bin/env bash
#
# Usage: ./install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]
#
# Example: ./install-wp-tests.sh wordpress_test root root 127.0.0.1 latest

set -e

if [ $# -lt 3 ]; then
    echo "Usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version]"
    exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}

WP_CORE_DIR=${WP_CORE_DIR-/tmp/wordpress}
WP_TESTS_DIR=${WP_TESTS_DIR-/tmp/wordpress-tests-lib}

download() {
    if command -v curl >/dev/null 2>&1; then
        curl -sL "$1" -o "$2"
    elif command -v wget >/dev/null 2>&1; then
        wget -q -O "$2" "$1"
    else
        echo "Error: curl or wget is required to download files."
        exit 1
    fi
}

install_wp() {
    if [ -d "$WP_CORE_DIR" ]; then
        echo "WordPress core already exists at $WP_CORE_DIR"
        return
    fi

    mkdir -p "$WP_CORE_DIR"

    if [ "$WP_VERSION" = "latest" ]; then
        ARCHIVE_URL="https://wordpress.org/latest.tar.gz"
    else
        ARCHIVE_URL="https://wordpress.org/wordpress-${WP_VERSION}.tar.gz"
    fi

    echo "Downloading WordPress $WP_VERSION..."
    download "$ARCHIVE_URL" /tmp/wordpress.tar.gz
    tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C "$WP_CORE_DIR"
}

install_test_suite() {
    if [ -d "$WP_TESTS_DIR" ]; then
        echo "WordPress test suite already exists at $WP_TESTS_DIR"
        return
    fi

    mkdir -p "$WP_TESTS_DIR"

    # Determine tag or trunk
    if [ "$WP_VERSION" = "latest" ]; then
        TAG="trunk"
    else
        TAG="tags/$WP_VERSION"
        if ! svn ls "https://develop.svn.wordpress.org/${TAG}" >/dev/null 2>&1; then
            echo "Tag $TAG not found, falling back to trunk..."
            TAG="trunk"
        fi
    fi

    echo "Checking out WordPress test suite ($TAG)..."
    svn co --quiet "https://develop.svn.wordpress.org/${TAG}/tests/phpunit/includes/" "$WP_TESTS_DIR/includes"
    svn co --quiet "https://develop.svn.wordpress.org/${TAG}/tests/phpunit/data/" "$WP_TESTS_DIR/data"

    echo "Copying wp-tests-config.php..."
    download "https://develop.svn.wordpress.org/${TAG}/wp-tests-config-sample.php" "$WP_TESTS_DIR/wp-tests-config.php"

    sed -i "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR/wp-tests-config.php"
    sed -i "s|localhost|$DB_HOST|" "$WP_TESTS_DIR/wp-tests-config.php"
}

install_db() {
    echo "Creating database if it doesn't exist..."
    mysqladmin create "$DB_NAME" --user="$DB_USER" --password="$DB_PASS" --host="$DB_HOST" --silent || true
}

# Run all steps
install_wp
install_test_suite
install_db

echo "✅ WordPress test environment is ready."