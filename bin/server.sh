#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
pushd $DIR/../server/
php73 artisan serve --port=1337 --host=0.0.0.0
popd
