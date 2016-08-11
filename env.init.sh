#!/bin/sh
## App Env Init Script


DIRS="logs"
EXECUTES="project/autoload_builder.sh"
SUBSYS="httpd server"

if test $# -lt 2 
then
    echo Usage: env.init.sh project_name who
    echo    eg: env.init.sh s3 cc
    exit
fi

USR=$2
PROJECT=$1
ROOT=`pwd`

echo create application environment for $USR

#if [ "$USR" != 'release' ]
#then
#    PHP=/usr/local/bin/php
#    $PHP $ROOT/install/env_build.php $USR $PROJECT
#    echo execute env_build.php succ;
#fi

# link app config file
cd $ROOT/config

for SUBSYS in $SUBSYS
do
    if test -e $SUBSYS\_conf.php
    then 
        rm $SUBSYS\_conf.php
    fi
    if (test -s $SUBSYS/$SUBSYS\_conf.php.$USR)
    then
        ln -s $SUBSYS/$SUBSYS\_conf.php.$USR $SUBSYS\_conf.php
        echo link -s $SUBSYS/$SUBSYS\_conf.php ........... OK
    else
        echo link -s $SUBSYS/$SUBSYS\_conf.php  ........... Fail
    fi 
done

# link http_conf to apache conf
if test -e httpd\_conf.php
    if test -e /etc/apache2/sites-available/$PROJECT\_$USR.conf
    then
        sudo rm -f /etc/apache2/sites-available/$PROJECT\_$USR.conf
    fi
then
    sudo ln -sf $ROOT/config/httpd\_conf.php /etc/apache2/sites-available/$PROJECT\_$USR.conf 
    echo link -s httpd\_conf.php to apache2/include/conf  .............OK
else
    echo link -s httpd\_conf.php to apache2/include/conf  .............Fail
fi

cd $ROOT
for dir in $DIRS
do
    if (test ! -d $dir)
    then
        mkdir -p $dir
        chmod 777 $dir
        echo mkdir $dir ................ OK
    fi
done

for execute in $EXECUTES
do
    sh $execute > /dev/null
    if test $? -eq 0
    then
        echo sh $execute ................ OK
    fi
done
