#!/bin/sh
## create files for env

PHP=/usr/local/php/bin/php
ROOT=`pwd`
$PHP $ROOT/project/env_build.php
