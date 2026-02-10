#!/bin/bash
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
echo Param=$1 $2 $3 $4
pwd
false
true



