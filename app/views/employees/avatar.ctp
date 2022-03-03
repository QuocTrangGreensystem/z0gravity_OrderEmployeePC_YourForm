<?php 
$name_avatar = strtoupper( substr($first_name,0,1).substr($last_name,0,1) );
$fullname = $first_name . ' ' .$last_name;
if( empty( $name_avatar ) ) $name_avatar = 'AV';

header('Content-Type: image/png');
// Create the image
list( $w, $h) = array( 40, 40);
$font_size = 14;
if( $type == 'thumb') { list( $w, $h) = array( 200, 200); $font_size = 72; }
$im = imagecreatetruecolor($w, $h);
// Create some colors
list($r, $g, $b) = sscanf($avatar_color, "#%02x%02x%02x");
$avt_backgr = imagecolorallocate($im, $r, $g, $b);
imagefilledrectangle($im, 0, 0, $w-1, $h-1, $avt_backgr);
$white = imagecolorallocate($im, 255, 255, 255);
$font = APP. 'webroot' . DS . 'fonts'. DS . 'opensans'. DS . 'OpenSans-SemiBold.ttf';
// Add the text
imagettftext($im, $font_size, 0, ($w/2 - $font_size*0.85), ($h + $font_size)/2, $white, $font, $name_avatar);
// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);
?>