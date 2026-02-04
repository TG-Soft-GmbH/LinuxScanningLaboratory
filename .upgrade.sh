#!/bin/bash
cd "$(dirname "$(realpath "${BASH_SOURCE[0]}")")"
git fetch --all 2>&1
git reset --hard origin/main 2>&1
./.postupgrade.sh 2>&1
ls -alh 2>&1
cat release.json 2>&1
