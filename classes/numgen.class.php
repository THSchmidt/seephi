<?php
class numgen {
	var $mainObj;

	function numgen($mainObj) {
		$this->mainObj = $mainObj;
	}


	function create_img() {
		$imageWidth = 120; # Set width of the image.
		$imageHeigth = 35; # Set height of the image.
		$image = imagecreate($imageWidth, $imageHeigth); # Creates a graphic object.
		$bgcolor = imagecolorallocate($image, 140, 140, 140); # Set background color (RGB).
		$color = imagecolorallocate($image, 60, 60, 60); # Set Text- and Lines-Color (RGB).

		srand ((double) microtime()*1000000); # Enables the following rand() functions to create random values, dependent from the current microtime.
		$string = (string) rand(10000, 99999); # 5-digits random string.
		$widthX = rand(10, 30); # Distance between two lines in x-Direction (left -> right).
		$widthY = rand(8, 20); # Distance between two lines in y-Direction (top -> bottom).
		$textX = array(rand(6,14), rand(26,34), rand(46,54), rand(66,74), rand(86,94)); # Create x-Coordinates of each characters.
		$textY = array(rand(20,30), rand(20,30), rand(20,30), rand(20,30), rand(20,30)); # Create y-Coordinates of each characters.
		$textAngle = array((360-rand(340,380)), (360-rand(340,380)), (360-rand(340,380)), (360-rand(340,380)), (360-rand(340,380))); # Create the angle of each character (-20,+20) [degrees].

		$x = 0;
		$x2 = 0;
		while($x <= $imageWidth) { # Draw the lines (top -> bottom) with rigid random distances, on x-Axis.
		    $x2 += $widthX;
		    imageline($image, $x, 0, $x2, $imageHeigth, $color);
		    $x = $x2;
		}

		$y = 0;
		$y2 = 0;
		while($y <= $imageHeigth) {  # Draw the lines (left -> right) with rigid random distances, on y-Axis.
		    $y2 += $widthY;
		    imageline($image, 0, $y, $imageWidth, $y2, $color);
		    $y = $y2;
		}

		for($i=0; $i<5; $i++) { # Write the random numbers (1 to 5) into the image (variable angles and starting coordinates (x and y); with font in the ttf-Format).
		    imagettftext($image, 25, $textAngle[$i], $textX[$i], $textY[$i], $color, $this->mainObj->incPath."font.ttf", $string[$i]); # Fontname: "A Cut Above The Rest".
		}

		$regKey = md5($string);
		imagejpeg($image, $this->mainObj->imgPath."numgen/".$regKey.".jpg", 100); # Write Image into a file (Filename = md5(Zufalls-String))
		return $regKey;
	}
}
?>
