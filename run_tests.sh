#!/bin/bash -x

cd src/

php artisan test  tests/Feature/*.php

cd ..