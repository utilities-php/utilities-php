#!/usr/bin/env bash

set -e
set -x

function extractHistory() {
  BRANCH=$2"-history"
  git subtree split -P $1 -b $BRANCH
  mkdir "tmp" && cd "tmp"
  git init
  git pull ../ $BRANCH
  git remote add origin $3
  git push origin HEAD --force
  cd ..
  rm -rf "tmp"
  git branch -D $BRANCH
}

extractHistory src/auth auth git@github.com:utilities-php/auth.git
extractHistory src/common common git@github.com:utilities-php/common.git
extractHistory src/database database git@github.com:utilities-php/database.git
extractHistory src/routing routing git@github.com:utilities-php/routing.git
extractHistory src/trader trader git@github.com:utilities-php/trader.git
extractHistory src/validator validator git@github.com:utilities-php/validator.git