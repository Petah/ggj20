#!/usr/bin/env bash

# Typography
COLOR_RED='\033[1;32m'
COLOR_INITIAL='\033[0m'

# Variables
SRC_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$SRC_DIR/../server"
CONFIG_FILE="$PROJECT_DIR/.prettierrc"
LINTER="$PROJECT_DIR/node_modules/.bin/prettier"
INCLUDE_DIRS="{app,resources}"
INCLUDE_EXTENSIONS="{js,json,scss,css,md}"

# Action
printf "${COLOR_RED}> Linting all static files${COLOR_INITIAL}\n"
$LINTER \
  --config $CONFIG_FILE \
  --write $PROJECT_DIR/$INCLUDE_DIRS/**/*.$INCLUDE_EXTENSIONS
