<?php

namespace Repainter\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Repainter\Http\Controllers\Controller;

class CompareController extends Controller
{
    public function compare(Request $request)
    {
        $imageBase64 = str_replace('data:image/png;base64,', '', $request->input('data.attributes.image'));
        $imageBinary = base64_decode($imageBase64);
        $image = imagecreatefromstring($imageBinary);
        $level = LevelController::IMAGES[$request->input('data.relationships.level.data.id')];
        $score = $this->getScore($image, public_path('images/levels/' . $level['image'] . '-repair.jpg'), public_path('images/levels/' . $level['image'] . '-actual.jpg'));
        return [
            'data' => [
                'type' => 'Comparison',
                'attributes' => [
                    'score' => $score,
                ],
            ],
        ];
    }

    /**
     * Compare two images and return score (100 means completely the same)
     *
     * @param $repairImageName a broken image to be repaired
     * @param $userImageName a broken image AFTER the user draws something
     * @param $actualImageName the original image
     * @throws Exception
     */
    private function getScore($userImage, string $repairImageName, string $actualImageName): float {

        //-------------------------------------------------------------------------
        // configuration
        $blurSize = 0; 			// 2: looking for 5x5 pixels: (blurSize * 2 + 1) x (blurSize * 2 + 1)
        $resizedWidth = 100;
        $resizedHeight = 100;
        //-------------------------------------------------------------------------

        $repairImage = imagecreatefromstring(file_get_contents($repairImageName));
        $actualImage = imagecreatefromstring(file_get_contents($actualImageName));

        if ($repairImage == null) {
            throw new Exception('repairImage is not found');
        }
        if ($userImage == null) {
            throw new Exception('userImage is not found');
        }
        if ($actualImage == null) {
            throw new Exception('actualImage is not found');
        }

        [$repairImageWidth, $repairImageHeight] = getimagesize($repairImageName);
        [$userImageWidth, $userImageHeight] = [imagesx($userImage), imagesy($userImage)];
        [$actualImageWidth, $actualImageHeight] = getimagesize($actualImageName);

        // var_dump("repairImage: width: $repairImageWidth, height: $repairImageHeight");
        // var_dump("userImage: width: $userImageWidth, height: $userImageHeight");
        // var_dump("actualImage: width: $actualImageWidth, height: $actualImageHeight");

        if ($repairImageWidth <= 0) {
            throw new Exception('repair image width is 0 or less');
        }
        if ($repairImageHeight <= 0) {
            throw new Exception('repair image height is 0 or less');
        }
        if ($userImageWidth <= 0) {
            throw new Exception('user image width is 0 or less');
        }
        if ($userImageHeight <= 0) {
            throw new Exception('user image height is 0 or less');
        }
        if ($actualImageWidth <= 0) {
            throw new Exception('actual image width is 0 or less');
        }
        if ($actualImageHeight <= 0) {
            throw new Exception('actual image height is 0 or less');
        }

        // var_dump("-----------------------------------");
        // var_dump("RESIZED SIZE: width: $resizedWidth, height: $resizedHeight");

        $resizedRepairImage = imagecreatetruecolor($resizedWidth, $resizedHeight);
        ImageColorTransparent($resizedRepairImage, imagecolorallocate($resizedRepairImage, 0, 0, 0));
        ImageAlphaBlending($resizedRepairImage, false);
        ImageCopyResampled($resizedRepairImage, $repairImage, 0, 0, 0, 0, $resizedWidth, $resizedHeight, ImageSX($repairImage), ImageSY($repairImage));

        $resizedUserImage = imagecreatetruecolor($resizedWidth, $resizedHeight);
        ImageColorTransparent($resizedUserImage, imagecolorallocate($resizedUserImage, 0, 0, 0));
        ImageAlphaBlending($resizedUserImage, false);
        ImageCopyResampled($resizedUserImage, $userImage, 0, 0, 0, 0, $resizedWidth, $resizedHeight, ImageSX($userImage), ImageSY($userImage));

        $resizedActualImage = imagecreatetruecolor($resizedWidth, $resizedHeight);
        ImageColorTransparent($resizedActualImage, imagecolorallocate($resizedActualImage, 0, 0, 0));
        ImageAlphaBlending($resizedActualImage, false);
        ImageCopyResampled($resizedActualImage, $actualImage, 0, 0, 0, 0, $resizedWidth, $resizedHeight, ImageSX($actualImage), ImageSY($actualImage));

        $repairImageWidth = imagesx($resizedRepairImage);
        $repairImageHeight = imagesy($resizedRepairImage);
        $userImageWidth = imagesx($resizedUserImage);
        $userImageHeight = imagesy($resizedUserImage);
        $actualImageWidth = imagesx($resizedActualImage);
        $actualImageHeight = imagesy($resizedActualImage);

        // var_dump("-----------------------------------");
        // var_dump("repairImage: width: $repairImageWidth, height: $repairImageHeight");
        // var_dump("userImage: width: $userImageWidth, height: $userImageHeight");
        // var_dump("actualImage: width: $actualImageWidth, height: $actualImageHeight");

        $numberOfPixels = $userImageWidth * $userImageHeight;
        // var_dump("numberOfPixels: $numberOfPixels");

        $repairScore = 0.0;
        $userScore = 0.0;

        for ($y = 0; $y < $actualImageHeight; $y++) {
            for ($x = 0; $x < $actualImageWidth; $x++) {

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

                // get color from repairImage
                $repairRed = 0.0;
                $repairGreen = 0.0;
                $repairBlue = 0.0;
                for ($repairY = $minimumY; $repairY <= $maximumY; $repairY++) {
                    for ($repairX = $minimumX; $repairX <= $maximumX; $repairX++) {
                        $color = imagecolorat($resizedRepairImage, $repairX, $repairY);
                        $red = ($color >> 16) & 0xFF;
                        $green = ($color >> 8) & 0xFF;
                        $blue = $color & 0xFF;

                        $repairRed += $red;
                        $repairGreen += $green;
                        $repairBlue += $blue;
                    }
                }
                $repairRed /= $numberOfBlurPixels;
                $repairGreen /= $numberOfBlurPixels;
                $repairBlue /= $numberOfBlurPixels;

                // get color from userImage
                $userRed = 0.0;
                $userGreen = 0.0;
                $userBlue = 0.0;
                for ($userY = $minimumY; $userY <= $maximumY; $userY++) {
                    for ($userX = $minimumX; $userX <= $maximumX; $userX++) {
                        $color = imagecolorat($resizedUserImage, $userX, $userY);
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
                        $color = imagecolorat($resizedActualImage, $actualX, $actualY);
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


                // calculate repairScore
                $repairDiffRed = $repairRed - $actualRed;
                $repairDiffGreen = $repairGreen - $actualGreen;
                $repairDiffBlue = $repairBlue - $actualBlue;
                if ($repairDiffRed < 0) {
                    $repairDiffRed *= -1;
                }
                if ($repairDiffGreen < 0) {
                    $repairDiffGreen *= -1;
                }
                if ($repairDiffBlue < 0) {
                    $repairDiffBlue *= -1;
                }
                $repairDiffRed = $repairDiffRed * 100.0 / 255.0;
                $repairDiffGreen = $repairDiffGreen * 100.0 / 255.0;
                $repairDiffBlue = $repairDiffBlue * 100.0 / 255.0;

                $repairScoreRed = 100.0 - $repairDiffRed;
                $repairScoreGreen = 100.0 - $repairDiffGreen;
                $repairScoreBlue = 100.0 - $repairDiffBlue;

                $repairScore += $repairScoreRed / 3.0 / $numberOfPixels;
                $repairScore += $repairScoreGreen / 3.0 / $numberOfPixels;
                $repairScore += $repairScoreBlue / 3.0 / $numberOfPixels;


                // calculate userScore
                $userDiffRed = $userRed - $actualRed;
                $userDiffGreen = $userGreen - $actualGreen;
                $userDiffBlue = $userBlue - $actualBlue;
                if ($userDiffRed < 0) {
                    $userDiffRed *= -1;
                }
                if ($userDiffGreen < 0) {
                    $userDiffGreen *= -1;
                }
                if ($userDiffBlue < 0) {
                    $userDiffBlue *= -1;
                }
                $userDiffRed = $userDiffRed * 100.0 / 255.0;
                $userDiffGreen = $userDiffGreen * 100.0 / 255.0;
                $userDiffBlue = $userDiffBlue * 100.0 / 255.0;

                $userScoreRed = 100.0 - $userDiffRed;
                $userScoreGreen = 100.0 - $userDiffGreen;
                $userScoreBlue = 100.0 - $userDiffBlue;

                $userScore += $userScoreRed / 3.0 / $numberOfPixels;
                $userScore += $userScoreGreen / 3.0 / $numberOfPixels;
                $userScore += $userScoreBlue / 3.0 / $numberOfPixels;
            }
        }
        // var_dump("repairScore: $repairScore, userScore: $userScore");

        if ($repairScore == 0.0 && $userScore == 0.0) {
            return 0.0;
        }
        if ($repairScore == 100.0) {
            return $userScore;
        } else {
            $score = ($userScore - $repairScore) / (100.0 - $repairScore) * 50.0 + 50.0;
            if ($score < 0) {
                $score = 0.0;
            }
            return $score;
        }
    }
}
