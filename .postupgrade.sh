#!/bin/bash
cp --update=none localconf.dist.php localconf.php

dir=$(sed -n "s/.*\$datastore *= *'\([^']*\)'.*/\1/p" localconf.php)
mkdir -p $dir
chmod a+rwx $dir

dir=$(sed -n "s/.*\$datastore_copy *= *'\([^']*\)'.*/\1/p" localconf.php)
mkdir -p $dir
chmod a+rwx $dir

sudo apt update
sudo apt -y install sane sane-utils sane-airscan
sudo apt -y autoremove
