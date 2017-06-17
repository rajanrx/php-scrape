#!/bin/bash
set -ex
export KOUNTA_CONFIG=test

# The port number here is not the standard fake_sqs port (4568) but it's the
# port our Docker containers use.
gem install fake_sqs
nohup fake_sqs --port 9494 --database docker/sqs/data/database.yml &

# Initialise test database.
mysql -e "DROP DATABASE IF EXISTS \`kounta-test-master\`;"
mysql -e "DROP DATABASE IF EXISTS \`kounta-test-data\`;"
mysql -e "CREATE DATABASE IF NOT EXISTS \`kounta-test-master\` CHARACTER SET utf8;"
mysql -e "CREATE DATABASE IF NOT EXISTS \`kounta-test-data\` CHARACTER SET utf8;"

# Run the test database migrations.
php protected/yiic migratemaster --interactive=0 > /tmp/out.txt 2>&1
if [ $? -ne 0 ]; then
    cat /tmp/out.txt
    exit 1
fi

php protected/yiic migrateshards --interactive=0 > /tmp/out.txt 2>&1
if [ $? -ne 0 ]; then
    cat /tmp/out.txt
    exit 1
fi

# Our unit test suite is split between multiple concurrent Travis jobs. The only
# safe way to evenly split all the test cases between the servers is to fetch a
# list of all the test case files (*Test.php) and then filter (remove) all that
# do no fit a modulo for that server number.
find protected/tests/unit addons/*/tests -name "*Test.php" | sort > all_tests.txt

# $WS represents the job number (starting at zero) and $W represents the total
# amount of jobs that will run concurrently. Here is an example of which servers
# will take specific test case files:
#
#   FooTest.php   <- Server 1 ($WS = 0)
#   BarTest.php   <- Server 2 ($WS = 1)
#   BazTest.php   <- Server 3 ($WS = 2)
#   QuxTest.php   <- Server 1 ($WS = 0)
#   QuuxTest.php  <- Server 2 ($WS = 1)
#   CorgeTest.php <- Server 3 ($WS = 2)
#
awk "NR % $WS != $W" all_tests.txt > remove_tests.txt
rm -rf $(< remove_tests.txt)

# There is a test suite called db_smoke which tests all the model classes
# against the real MySQL tables and ensures all the columns match up. Under dev
# this is unnecessary, but we want to run it on Travis. So there is a patch that
# adds that test suite to the PHPUnit configuration.
#
# The reason we do it this way rather than just using the CLI to run the suite
# specifically is that there are a lot of model tests and we want them to be
# spread over all the running jobs, as explained above.
git apply config/phpunit.xml.patch
grep -vi strict phpunit.xml > phpunit.xml.tmp; mv -f phpunit.xml.tmp phpunit.xml

# Run PHPUnit now, only files that are left after the pruning will be run now.
# Since we are not using code coverage on Travis right now make sure we run it
# without Xdebug so the tests are faster. (Xdebug is disabled in .travis.yml.)
vendor/bin/concise --ci --exclude-group redshift
