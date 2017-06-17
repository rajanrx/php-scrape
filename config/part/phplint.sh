#!/bin/bash
set -ex

# The kounta_phpunit container (rather than kounta_web) is configured with a
# much greater memory_limit so that it can perform tasks like this.
if [ -z "$TRAVIS" ]; then
    export KOUNTA_PHPUNIT="docker exec -i -t kounta_phpunit_1 sh -c"
else
    export KOUNTA_PHPUNIT="sh -c"
fi
export SRCS="addons protected/commands protected/components protected/controllers protected/lib protected/models"

# phplint
$KOUNTA_PHPUNIT 'find . -name "*.php" | egrep -v ^./vendor | xargs -n1 -P8 php -l'

# Any cache generated should be removed now
rm -rf protected/lib/Kounta/Config/cache

# phpmd
for SRC in $SRCS
do
    echo "phpmd: $SRC"
    $KOUNTA_PHPUNIT "vendor/bin/phpmd --exclude '*Test.php,*TestCase.php' $SRC text codesize"
done

# phpcpd
$KOUNTA_PHPUNIT "vendor/bin/phpcpd \
    --fuzzy --min-lines=1 --min-tokens=74 \
    \$(find $SRCS | \
        grep -v protected/lib/Kounta/Config/cache | \
        grep -v /views/ | \
        grep .php | \
        grep -v Test.php)"

# phpcs
$KOUNTA_PHPUNIT 'vendor/bin/phpcs -p'

# validate views
$KOUNTA_PHPUNIT 'php config/part/validate_views.php'

# check for deprecated php & js expressions
$KOUNTA_PHPUNIT 'php config/part/check_deprecated.php'
