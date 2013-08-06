<?php
session_start();

class CaptchaSecurityImages {

   var $font = 'DoradoHeadline.ttf';

   function generateCode($characters) {
      /* list all possible characters, similar looking characters and vowels have been removed */
      $possible = 'BCDFGHJKMNPQRSTVWXYZ23456789bcdfghjkmnpqrstvwxyz';
      $code = '';
      $i = 0;
      while ($i < $characters) {
         $code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
         $i++;
      }
      return $code;
   }

   function CaptchaSecurityImages($width='120',$height='40',$characters='6') {

      $code = $this->generateCode($characters);

      /* font size will be 75% of the image height */
      $font_size = $height * 0.50;
      $image = imagecreate($width, $height) or die('Cannot initialize new GD image stream');

      /* set the colours */
      $background_color = imagecolorallocate($image, 255, 255, 255);

      /* red text */
      //$text_color = imagecolorallocate($image, 220, 10, 10);

      /* black text */
      $text_color = imagecolorallocate($image, 1, 1, 1);

	$rndR = rand(98,198);
	$rndG = rand(98,198);
	$rndB = rand(98,198);
      $noise_color = imagecolorallocate($image, $rndR, $rndG, $rndB);

      /* generate random dots in background */
      for( $i=0; $i<($width*$height)/3; $i++ ) {
         imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
      }

      /* generate random lines in background */
      for( $i=0; $i<($width*$height)/150; $i++ ) {
         imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
      }

      /* create textbox and add text */
      $textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
      $x = ($width - $textbox[4])/2;
      $y = ($height - $textbox[5])/2;
      imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');

      // Show the image.
	if (function_exists('imagegif'))
	{
		header('Content-type: image/gif');
		imagegif($image);
	}
	else
	{
		header('Content-type: image/png');
		imagepng($image);
	}

	// Bail out.
	imagedestroy($image);

      $_SESSION['security_code'] = $code;

      die();


   }

}

$width = isset($_GET['width']) && $_GET['height'] < 600 ? $_GET['width'] : '132';
$height = isset($_GET['height']) && $_GET['height'] < 200 ? $_GET['height'] : '36';
$characters = isset($_GET['characters']) && $_GET['characters'] > 2 ? $_GET['characters'] : '6';

$captcha = new CaptchaSecurityImages($width,$height,$characters);

?>