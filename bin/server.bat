pushd %~dp0\..\server
php artisan serve --port=1337 --host=0.0.0.0
popd
