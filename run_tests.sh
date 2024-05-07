#!/bin/bash

cd src/

php artisan test  tests/Feature/*.php

cd ..