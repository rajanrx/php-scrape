set -ex

# http://stackoverflow.com/a/20939051/1470961
ssh -o StrictHostKeyChecking=no git@github.com || true

curl -L https://github.com/nikic/php-ast/archive/master.zip > /tmp/master.zip
cd /tmp && unzip master.zip
cd /tmp/php-ast-master && phpize && ./configure && make && make install
echo "extension=ast.so" >> $HOME/.phpenv/versions/$(phpenv version-name)/etc/php.ini
cd $TRAVIS_BUILD_DIR

# Install phan
composer global require etsy/phan=0.9.2

# Run phan check
time $HOME/.composer/vendor/bin/phan -pqj 1
