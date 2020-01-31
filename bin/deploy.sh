#!/usr/bin/env bash

# Typography
COLOR_RED='\033[1;32m'
COLOR_INITIAL='\033[0m'

# Deployment paths
SRC_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$SRC_DIR/../server"

# Sync project files
pushd $PROJECT_DIR > /dev/null
printf "${COLOR_RED}> Syncing files between local and server${COLOR_INITIAL}\n"
rsync --compress --verbose --recursive --checksum environment/sitehost/.env ggj20@ggj20.versite.co.nz:/container/application/.env
rsync --compress --verbose --recursive --checksum artisan ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum composer.json ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum composer.lock ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum app ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum bootstrap ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum config ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum database ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum public ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum resources ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum routes ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum storage ggj20@ggj20.versite.co.nz:/container/application
rsync --compress --verbose --recursive --checksum vendor ggj20@ggj20.versite.co.nz:/container/application
popd > /dev/null

# Run DB migrations, and flush cache and templates
printf "${COLOR_RED}> Running DB migrations, and flushing cache and templates${COLOR_INITIAL}\n"
# ssh ggj20@ggj20.versite.co.nz "cd /container/application ; php artisan optimize"
# ssh ggj20@ggj20.versite.co.nz "cd /container/application ; php artisan migrate --force"

# Success!
printf "${COLOR_RED}> Deployment complete${COLOR_INITIAL}\n"
