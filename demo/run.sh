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
composer require zendframework/zftool:dev-master;

rm -R module/ExampleModule;
php vendor/bin/zf.php create module ExampleModule;
rm -R module/ExampleModule;
cd module;
ln -s ../../ExampleModule;
cd - >/dev/null;

php -S 0.0.0.0:8080 -t public

