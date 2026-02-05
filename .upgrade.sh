#!/bin/bash
cd "$(dirname "$(realpath "${BASH_SOURCE[0]}")")"
dir=$PWD; while [[ $dir != / && ! -d $dir/.git ]]; do dir=${dir%/*}; done; [[ -d $dir/.git ]] && cd "$dir" || exit;
echo "Upgrading git repo in $PWD"
git fetch --all 2>&1
git reset --hard origin/main 2>&1
./.postupgrade.sh 2>&1
ls -alh 2>&1
cat release.json 2>&1
