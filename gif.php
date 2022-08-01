<?php

	//Leave all this stuff as it is
	//date_default_timezone_set('Europe/London');
	include 'GIFEncoder.class.php';
	include 'php52-fix.php';
	$time = $_GET['time'];
	$timezone = $_GET['timezone'];
	$language = $_GET['language'];
	date_default_timezone_set($timezone);
	//$time = '2022-06-25+00:00:01';
	$future_date = new DateTime(date('r',strtotime($time)));
	$time_now = time();
	$now = new DateTime(date('r', $time_now));
	$frames = [];	
	$delays = [];

	// Your image link
	$image = getImage($language);

	$delay = 100;// milliseconds

	$font = [
		'size' => 20, // Font size, in pts usually.
		'angle' => 0, // Angle of the text
		'x-offset' => 7, // The larger the number the further the distance from the left hand side, 0 to align to the left.
		'y-offset' => 30, // The vertical alignment, trial and error between 20 and 60.
		'file' => __DIR__ . DIRECTORY_SEPARATOR . 'Futura.ttc', // Font path
		'color' => imagecolorallocate($image, 55, 160, 130), // RGB Colour of the text
	];
	for($i = 0; $i <= 60; $i++){
		
		$interval = date_diff($future_date, $now);
		
		if($future_date < $now){
			// Open the first source image and add the text.
			$image = getImage($language);
			
			$text = $interval->format('00:00:00:00');
			imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset'] , $font['y-offset'] , $font['color'] , $font['file'], $text );
			ob_start();
			imagegif($image);
			$frames[]=ob_get_contents();
			$delays[]=$delay;
			$loops = 1;
			ob_end_clean();
			break;
		} else {
			// Open the first source image and add the text.
			$image = getImage($language);
			
			$text = $interval->format('0%a %H %I %S');
			imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset'] , $font['y-offset'] , $font['color'] , $font['file'], $text );
			ob_start();
			imagegif($image);
			$frames[]=ob_get_contents();
			$delays[]=$delay;
			$loops = 0;
			ob_end_clean();
		}

		$now->modify('+1 second');
	}

//
function getImage($language)
{
	switch ($language)
	{
	case 'ES':
		$image = imagecreatefrompng('images/countdown-ES.png');
		break;
	case 'EN':
		$image = imagecreatefrompng('images/countdown-EN.png');
		break;
	/*case 'DE':
		$image = imagecreatefrompng('images/countdown.png');
		break;
	case 'FR':
		$image = imagecreatefrompng('images/countdown.png');
		break;
	case 'IT':
		$image = imagecreatefrompng('images/countdown.png');
		break;
	case 'NL':
		$image = imagecreatefrompng('images/countdown.png');
		break;
	case 'PT':
		$image = imagecreatefrompng('images/countdown.png');
		break;*/
	default:
		$image = imagecreatefrompng('images/countdown.png');
		break;
	}
	return $image;
}

	//expire this image instantly
	header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', false );
	header( 'Pragma: no-cache' );
	$gif = new AnimatedGif($frames,$delays,$loops);
	$gif->display();
