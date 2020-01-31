<?php
$userImage = imagecreatefrompng(__DIR__ . '/user-image.png');
$actualImage = imagecreatefrompng(__DIR__ . '/actual-image.png');

$userImageSize = getimagesize(__DIR__ . '/user-image.png');
var_dump($userImageSize);
// etc