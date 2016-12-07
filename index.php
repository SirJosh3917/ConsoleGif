<?php

//Include GifCreator to easily make gifs
include "GifCreator.php";

//Set some constants
$Width = 0;
$Height = 16;
$Background = 0x000000;
$FontName = "console.ttf";
$FontWidth = 8;
$FontWidthAdd = 16;
$DurationText = 10;
$DurationCursor = 50;

//Set default values
$text = "C:\\>ping %ip -n 4"; //Console Text
$hex = 0xFFFFFF; //Console Hex
$frames = array(); //GIF Frames
$durations = array(); //Duration of each frame
$col = true;
$durationTime = $DurationText;

//Parse $_GET
if(isset($_GET["val"])) {
	$text = $_GET["val"];
}

if(isset($_GET["hex"])) {
	$hex = $_GET["hex"];
}

//Parse the text
$text = str_replace("%ip", $_SERVER['REMOTE_ADDR'], $text);

//Set this to the length of the text
$strlentext = strlen($text);

//If the text is greater than 60, we'll crop it to size.
if($strlentext > 60) {
	$text = substr($text, 0, 60);
$strlentext = strlen($text);
}

if(isset($_GET["hex"])) {
	$hex = $_GET["hex"];
}

for($i = 0; $i < $strlentext + 10; $i++) {
	
	//Set the duration time of the gif frame
	$durationTime = $DurationText;
	
	//Get the small piece of text to draw
	if($i > $strlentext) {
		$texttmp = substr($text, 0, $strlentext);
	} else {
		$texttmp = substr($text, 0, $i + 1);
	}
	
	//Create a new image for the text
	$img = imagecreatetruecolor(($strlentext * $FontWidth) + $FontWidthAdd, $Height);
	
	//Fill the background
	imagefilledrectangle($img, 0, 0, ($strlentext * $FontWidth) + ($FontWidthAdd * 2), $Height - 1, $Background);
	
	//Draw the text
	imagettftext( $img, $Height - 6, 0, 1, $Height - 5, $hex, $FontName, $texttmp);
	
	//Check if we are going over the length of the text, we will draw the cursor.
	if($i > $strlentext) {
		if(!$col) {
			imagefilledrectangle($img, ($strlentext * $FontWidth)  + ($FontWidthAdd / 2) - 2, $Height - 6, ($strlentext * $FontWidth) + ( $FontWidthAdd - 2 ), $Height - 3, $hex);
			$col = false;
		}
		
		//Toggle the console cursor box on and off
		$col = !$col;
		
		//Change the duration tume because we are animating the cursor
		$durationTime = $DurationCursor;
	}
	
	//Set the duration time and the frame with the image
	$durations[$i] = $durationTime;
	$frames[$i] = $img;
}

// Initialize the GifCreator
$gc = new GifCreator();

//Create it
$gc->create($frames, $durations, $strlentext + 1);

//Get the binary
$gifBinary = $gc->getGif();


//Set headers for outputting the gif
header('Content-type: image/gif');
header('Content-Disposition: filename="console.gif"');

//Tell the client about the gif.
echo $gifBinary;

//Destroy the gif
imagedestroy($gifBinary);

//Destroy each frame of the gif
for($i = 0; $i < $strlentext + 1; $i++) {
	imagedestroy($frames[$i]); //Destroy one of the images in the frame
}

exit;
?>
