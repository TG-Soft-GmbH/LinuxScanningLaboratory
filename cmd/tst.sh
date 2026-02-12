#!/bin/bash
echo Param1=$1
echo Param2=$2
echo Param3=$3
echo Param4=$4
sleep 1
echo 0
sleep 1
echo 1
sleep 1
echo 2 >&2
sleep 1
echo 3
sleep 1
echo 4 >&2
sleep 1
pwd
false
true