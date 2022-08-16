#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="master"

function split()
{
    SHA1=$(./splitsh-lite-linux --prefix=$1)
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

function remote()
{
    git remote add $1 $2 || true
}

git pull origin $CURRENT_BRANCH

remote auth git@github.com:utilities-php/common.git
remote auth git@github.com:utilities-php/database.git
remote auth git@github.com:utilities-php/router.git
remote trader git@github.com:utilities-php/trader.git

split 'src/common' common
split 'src/database' database
split 'src/router' router
split 'src/trader' trader
