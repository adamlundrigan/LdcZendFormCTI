#!/bin/bash

if [ ! -d ZendSkeletonApplication ]
then
    git clone https://github.com/zendframework/ZendSkeletonApplication.git;
    cd ZendSkeletonApplication;
else
    cd ZendSkeletonApplication;
    git reset --hard origin/master;
fi;

composer install --dev;

cp ../application.config.php.dist config/application.config.php;

rm -R module/ExampleModule;
cd module;
ln -s ../../ExampleModule;
cd - >/dev/null;

php -S 0.0.0.0:8080 -t public

