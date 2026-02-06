#!/bin/bash
sudo apt update
sudo apt -y install sane sane-utils sane-airscan
sudo apt -y autoremove
chmod a+rwx scandata

