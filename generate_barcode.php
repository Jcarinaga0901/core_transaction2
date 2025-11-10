<?php
// Generate barcode image for waybill tracking numbers
header('Content-Type: image/png');

// Get tracking number from URL parameter
$trackingNumber = $_GET['code'] ?? 'TRK00000001';
$width = $_GET['width'] ?? 300;
$height = $_GET['height'] ?? 100;

// Create image
$image = imagecreate($width, $height);

// Define colors
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);

// Fill background with white
imagefill($image, 0, 0, $white);

// Generate simple barcode pattern
$barWidth = 2;
$x = 10;
$y = 20;

// Create barcode pattern based on tracking number
$code = str_pad($trackingNumber, 12, '0', STR_PAD_LEFT);
for ($i = 0; $i < strlen($code); $i++) {
    $digit = intval($code[$i]);
    $barHeight = 40 + ($digit * 3);
    
    // Draw bar
    imagefilledrectangle($image, $x, $y, $x + $barWidth, $y + $barHeight, $black);
    $x += $barWidth + 1;
}

// Add tracking number text below barcode
$font = 3;
$textWidth = imagefontwidth($font) * strlen($trackingNumber);
$textX = ($width - $textWidth) / 2;
$textY = $y + 50;

imagestring($image, $font, $textX, $textY, $trackingNumber, $black);

// Output image
imagepng($image);
imagedestroy($image);
?>