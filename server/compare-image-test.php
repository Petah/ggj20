<?php

// compare two images and return score (100 means completelt the same)
//
// error code
// -101: userImage is not found
// -102: actualImage is not found
// -201: image size is not the same (width)
// -202: image size is not the same (height)
// -203: user image width is 0 or less
// -204: user image height is 0 or less
// -205: actual image width is 0 or less
// -206: actual image height is 0 or less
function getScore($userImageName, $actualImageName) {

	$blurSize = 0; // looking for 5x5 pixels: (blurSize * 2 + 1) x (blurSize * 2 + 1)

	$userImage = imagecreatefrompng($userImageName);
	$actualImage = imagecreatefrompng($actualImageName);

	if ($userImage == null) {
		throw new \Exception('userImage is not found');
	}
	if ($actualImage == null) {
		throw new \Exception('actualImage is not found');
	}

	[$userImageWidth, $userImageHeight] = getimagesize($userImageName);
    $userImageResized = imagecreatetruecolor(100, 100);
    imagecopyresampled($userImageResized, $userImage, 0, 0, 0, 0, 100, 100, $userImageWidth, $userImageHeight);
	[$actualImageWidth, $actualImageHeight] = getimagesize($actualImageName);

	var_dump("userImageWidth: $userImageWidth");
	var_dump("actualImageWidth: $actualImageWidth");
	var_dump("userImageHeight: $userImageHeight");
	var_dump("actualImageHeight: $actualImageHeight");

	if ($userImageWidth != $actualImageWidth) {
		return -201;
	}
	if ($userImageHeight != $actualImageHeight) {
		return -202;
	}
	if ($userImageWidth <= 0) {
		return -203;
	}
	if ($userImageHeight <= 0) {
		return -204;
	}
	if ($actualImageWidth <= 0) {
		return -205;
	}
	if ($actualImageHeight <= 0) {
		return -206;
	}
	$numberOfPixels = $userImageWidth * $userImageHeight;

	$score = 0.0;

	for ($y = 0; $y < $userImageHeight; $y++) {
		for ($x = 0; $x < $userImageWidth; $x++) {

			// calciulate range for blur
			$minimumX = $x - $blurSize;
			if ($minimumX < 0) {
				$minimumX = 0;
			}
			$maximumX = $x + $blurSize;
			if ($maximumX >= $userImageWidth) {
				$maximumX = $userImageWidth - 1;
			}
			$minimumY = $y - $blurSize;
			if ($minimumY < 0) {
				$minimumY = 0;
			}
			$maximumY = $y + $blurSize;
			if ($maximumY >= $userImageHeight) {
				$maximumY = $userImageHeight - 1;
			}
			$numberOfBlurPixels = ($maximumX - $minimumX + 1) * ($maximumY - $minimumY + 1);

			// get color from userImage
			$userRed = 0.0;
			$userGreen = 0.0;
			$userBlue = 0.0;
			for ($userX = $minimumX; $userX <= $maximumX; $userX++) {
				for ($userY = $minimumY; $userY <= $maximumY; $userY++) {
					$color = imagecolorat($userImage, $userX, $userY);
					$red = ($color >> 16) & 0xFF;
					$green = ($color >> 8) & 0xFF;
					$blue = $color & 0xFF;

					$userRed += $red;
					$userGreen += $green;
					$userBlue += $blue;
				}
			}
			$userRed /= $numberOfBlurPixels;
			$userGreen /= $numberOfBlurPixels;
			$userBlue /= $numberOfBlurPixels;

			// get color from actualImage
			$actualRed = 0.0;
			$actualGreen = 0.0;
			$actualBlue = 0.0;
			for ($actualX = $minimumX; $actualX <= $maximumX; $actualX++) {
				for ($actualY = $minimumY; $actualY <= $maximumY; $actualY++) {
					$color = imagecolorat($actualImage, $actualX, $actualY);
					$red = ($color >> 16) & 0xFF;
					$green = ($color >> 8) & 0xFF;
					$blue = $color & 0xFF;

					$actualRed += $red;
					$actualGreen += $green;
					$actualBlue += $blue;
				}
			}
			$actualRed /= $numberOfBlurPixels;
			$actualGreen /= $numberOfBlurPixels;
			$actualBlue /= $numberOfBlurPixels;


			// calculate score
			echo $x . ' ' . $y . ' ' . $userRed  . ' ' .  $actualRed . PHP_EOL;
			$diffRed = $userRed - $actualRed;
			$diffGreen = $userGreen - $actualGreen;
			$diffBlue = $userBlue - $actualBlue;
			if ($diffRed < 0) {
				$diffRed *= -1;
			}
			if ($diffGreen < 0) {
				$diffGreen *= -1;
			}
			if ($diffBlue < 0) {
				$diffBlue *= -1;
			}
			$diffRed = 100.0 - $diffRed * 100.0 / 255.0;
			$diffGreen = 100.0 - $diffGreen * 100.0 / 255.0;
			$diffBlue = 100.0 - $diffBlue * 100.0 / 255.0;

			$score += $diffRed / 3.0 / $numberOfPixels;
			$score += $diffGreen / 3.0 / $numberOfPixels;
			$score += $diffBlue / 3.0 / $numberOfPixels;
		}
	}
	return $score;
}

var_dump("### start");
$result = getScore(__DIR__ . '/public/images/levels/smiley-repair.png', __DIR__ . '/public/images/levels/smiley-actual.png');
$result = getScore(__DIR__ . '/public/images/levels/smiley-user.png', __DIR__ . '/public/images/levels/smiley-actual.png');
var_dump("### score: $result");
