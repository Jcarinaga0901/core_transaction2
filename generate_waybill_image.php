<?php
// Generate J&T Express style waybill image
header('Content-Type: image/png');

// Get parameters
$deliveryId = $_GET['id'] ?? 1;
$width = $_GET['width'] ?? 800;
$height = $_GET['height'] ?? 1000;

// Create image
$image = imagecreate($width, $height);

// Define colors
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$red = imagecolorallocate($image, 220, 20, 20);
$gray = imagecolorallocate($image, 128, 128, 128);
$lightGray = imagecolorallocate($image, 240, 240, 240);
$orange = imagecolorallocate($image, 255, 140, 0);

// Fill background
imagefill($image, 0, 0, $white);

$y = 20;

// J&T EXPRESS Header
imagestring($image, 5, 20, $y, "J&T EXPRESS", $red);
imagestring($image, 3, 20, $y + 25, "RAEVOR", $black);

// Location and date
imagestring($image, 3, 200, $y, "Manila", $black);
imagestring($image, 3, 200, $y + 20, "Send Date: " . date('Y-m-d'), $black);

// Order number in top right
$orderNumber = date('ymd') . 'QHUEWDGF';
imagestring($image, 3, $width - 150, $y, "1118", $black);
imagestring($image, 3, $width - 200, $y + 20, "Order ID: " . $orderNumber, $black);

$y += 60;

// Large tracking number
$trackingNumber = '244-' . str_pad($deliveryId, 6, '0', STR_PAD_LEFT);
imagestring($image, 6, 20, $y, $trackingNumber, $black);
$y += 50;

// Barcode
$barcodeNumber = '781234907377';
imagestring($image, 4, 20, $y, $barcodeNumber, $black);
$y += 30;

// Draw simple barcode
$barX = 20;
$barY = $y;
$barWidth = 3;
$barHeight = 40;

for ($i = 0; $i < 15; $i++) {
    $digit = rand(0, 9);
    $currentBarHeight = $barHeight + ($digit * 2);
    
    if ($i % 2 == 0) {
        imagefilledrectangle($image, $barX, $barY, $barX + $barWidth, $barY + $currentBarHeight, $black);
    }
    $barX += $barWidth + 1;
}

$y += 80;

// Draw separator line
imageline($image, 20, $y, $width - 20, $y, $black);
$y += 20;

// BUYER section
imagestring($image, 4, 20, $y, "BUYER", $black);
$y += 30;

// Buyer info
$buyerInfo = [
    "Name: Christopher Enso",
    "Contact Number: 639071742501",
    "Address: 47-B Sitio Ruby East Fairview Quezon City, Quezon City, Metro Manila",
    "City/District: Quezon City, Fairview",
    "Region: Metro Manila"
];

foreach ($buyerInfo as $info) {
    imagestring($image, 3, 30, $y, $info, $black);
    $y += 25;
}

// Add 1118 in top right of buyer section
imagestring($image, 3, $width - 50, $y - 100, "1118", $black);

$y += 20;

// Draw separator line
imageline($image, 20, $y, $width - 20, $y, $black);
$y += 20;

// SELLER section
imagestring($image, 4, 20, $y, "SELLER", $black);
$y += 30;

// Seller info
$sellerInfo = [
    "Name: Ofelia Despuig",
    "Contact Number: 639076625954",
    "Address: Purok rosal caduangtete macabebe pampanga house number 074",
    "City/District: Macabebe, Caduang Tete",
    "Region: Pampanga, North Luzon"
];

foreach ($sellerInfo as $info) {
    imagestring($image, 3, 30, $y, $info, $black);
    $y += 25;
}

// Add 2018 in bottom right of seller section
imagestring($image, 3, $width - 50, $y - 20, "2018", $black);

$y += 20;

// Draw separator line
imageline($image, 20, $y, $width - 20, $y, $black);
$y += 20;

// Bottom section with QR code and product details
$bottomY = $height - 150;

// QR Code (left side)
$qrSize = 80;
$qrX = 20;
$qrY = $bottomY;
imagefilledrectangle($image, $qrX, $qrY, $qrX + $qrSize, $qrY + $qrSize, $lightGray);
imagerectangle($image, $qrX, $qrY, $qrX + $qrSize, $qrY + $qrSize, $black);

// Add QR text
imagestring($image, 2, $qrX + 10, $qrY + 30, "QR CODE", $black);

// Product details (right side of QR)
$productX = $qrX + $qrSize + 20;
imagestring($image, 3, $productX, $bottomY, "Product Quantity: 1", $black);
imagestring($image, 3, $productX, $bottomY + 25, "Weight: 1.0 kg", $black);
imagestring($image, 3, $productX, $bottomY + 50, "COD Amount: 0", $black);

// Delivery and Return attempts
$attemptY = $bottomY + 80;
imagestring($image, 3, 20, $attemptY, "Delivery Attempt:", $black);
imagestring($image, 3, 150, $attemptY, "1", $black);
imagestring($image, 3, 180, $attemptY, "2", $black);

imagestring($image, 3, 20, $attemptY + 25, "Return Attempt:", $black);
imagestring($image, 3, 150, $attemptY + 25, "1", $black);
imagestring($image, 3, 180, $attemptY + 25, "2", $black);

// Shopee logo and text at bottom
imagestring($image, 3, 20, $height - 30, "Thank you for shipping with RAEVOR! Please click 'Order Received and rate this product!!'", $orange);

// Output image
imagepng($image);
imagedestroy($image);
?>