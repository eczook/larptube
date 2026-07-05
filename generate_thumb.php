<?php
$img = imagecreate(120, 90);
$bg = imagecolorallocate($img, 51, 51, 51);
$text_color = imagecolorallocate($img, 255, 255, 255);
imagestring($img, 3, 30, 40, "No Thumb", $text_color);
imagejpeg($img, 'images/default_thumb.jpg');
imagedestroy($img);
echo "Default thumbnail created!";
?>