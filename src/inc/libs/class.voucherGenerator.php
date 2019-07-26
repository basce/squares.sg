<?php 
class voucherGenerator{
	private static function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct, $trans = NULL)
	{
	  $dst_w = imagesx($dst_im);
	  $dst_h = imagesy($dst_im);
	
	  // bounds checking
	  $src_x = max($src_x, 0);
	  $src_y = max($src_y, 0);
	  $dst_x = max($dst_x, 0);
	  $dst_y = max($dst_y, 0);
	  if ($dst_x + $src_w > $dst_w)
		$src_w = $dst_w - $dst_x;
	  if ($dst_y + $src_h > $dst_h)
		$src_h = $dst_h - $dst_y;
	
	  for($x_offset = 0; $x_offset < $src_w; $x_offset++)
		for($y_offset = 0; $y_offset < $src_h; $y_offset++)
		{
		  // get source & dest color
		  $srccolor = imagecolorsforindex($src_im, imagecolorat($src_im, $src_x + $x_offset, $src_y + $y_offset));
		  $dstcolor = imagecolorsforindex($dst_im, imagecolorat($dst_im, $dst_x + $x_offset, $dst_y + $y_offset));
	
		  // apply transparency
		  if (is_null($trans) || ($srccolor !== $trans))
		  {
			$src_a = $srccolor['alpha'] * $pct / 100;
			// blend
			$src_a = 127 - $src_a;
			$dst_a = 127 - $dstcolor['alpha'];
			$dst_r = ($srccolor['red'] * $src_a + $dstcolor['red'] * $dst_a * (127 - $src_a) / 127) / 127;
			$dst_g = ($srccolor['green'] * $src_a + $dstcolor['green'] * $dst_a * (127 - $src_a) / 127) / 127;
			$dst_b = ($srccolor['blue'] * $src_a + $dstcolor['blue'] * $dst_a * (127 - $src_a) / 127) / 127;
			$dst_a = 127 - ($src_a + $dst_a * (127 - $src_a) / 127);
			$color = imagecolorallocatealpha($dst_im, $dst_r, $dst_g, $dst_b, $dst_a);
			// paint
			if (!imagesetpixel($dst_im, $dst_x + $x_offset, $dst_y + $y_offset, $color))
			  return false;
			imagecolordeallocate($dst_im, $color);
		  }
		}
	  return true;
	}
	
	public static function createVoucher($name, $prizename, $serial, $cardBackground, $extra_tnc='')
	{
		$fontsize = 42;

		//hardcode information for testing
		//$name = "Charissa Chen";
		//$prizename = "Complimentary Steamed Sesame Buns (3pcs) (worth $4.80) with min. spend of $68";
		//$serial = "00025388961";

		$cardImage = imagecreatefromjpeg($cardBackground);

		//$backgroundColor = imagecolorallocate ($cardImage, 255, 255, 255);
		$textColor = imagecolorallocate ($cardImage, 255, 255,255);

		$txtheadert1 = "A little something";
		$txtheadert2 = "from us...";
		$txtheadert3 = "_____";
		$txtheadert4 = "Dear ";
		$txtheadert5 = $name.",";
		$txtheadert6 = "Thank you for joining us at Happy Diners.";
		$txtheadert7 = "Enjoy a:";
		//$txtheadert8 = "Complimentary Steamed Sesame";
		//$txtheadert9 = "Buns (3pcs) (worth $4.80) with a";
		//$txtheadert10 = "min. spend of $68";
		$txtheadert11 = "e-voucher serial number";
		$txtheadert12 = $serial;
		$txtheadert13 = "Valid between";
		$txtheadert14 = "Fri, 4 Dec 2015 – Sun, 17 Jan 2016.";
		$txtheadert15 = "Redeemable only at the following restaurants:";
		$txtheadert16 = "Paragon B1-03 . Tampines Mall 02-01. Jurong Point B1-68. Nex B1-10.";
		$txtheadert17 = "Suntec City 02-302. Bedok Mall B1-10. Manulife Centre 01-02. The Seletar Mall 02-07.";
		$txtheadert18 = "Terms & Conditions";
		if ($extra_tnc != '')
		{
			$txtheadert19 = "All prices stated are before GST & service charge. Only valid with presentation of voucher before ordering (both presentation of mobile";
			$txtheadert20 = "screen/printout are accepted). The actual design is based on availability at the point of redemption & are non-exchangeable. The gift is not";
			$txtheadert21 = "exchangeable for cash, or other gift items. Not applicable with other promotions, discounts, vouchers and/or privileges. Valid for dine-in only.";
			$txtheadert22 = "Valid while stocks/servings last. Valid for one complimentary item per table per bill. Management reserves the right to withdraw or amend offer";
			imagettftext ($cardImage, 7.5, 0, 82, 569, $textColor, 'fonts/MerriweatherSans-Light.ttf', "at any time. Terms & conditions are subject to change without prior notice.");
		}
		else
		{
			$txtheadert19 = "All prices stated are before GST & service charge. Only valid with presentation of voucher before ordering (both presentation of mobile";
			$txtheadert20 = "screen/printout are accepted). Not applicable with other promotions, discounts, vouchers and/or privileges. Valid for dine-in only. Valid while";
			$txtheadert21 = "stocks/servings last. Valid for one complimentary item per table per bill. Management reserves the right to withdraw or amend offer at any time.";
			$txtheadert22 = "Terms & conditions are subject to change without prior notice.";
		}
		
		imagettftext ($cardImage, 26, 0, 82, 105, $textColor, 'fonts/Merriweather-Italic.ttf', $txtheadert1);
		imagettftext ($cardImage, 26, 0, 82, 140, $textColor, 'fonts/Merriweather-Italic.ttf', $txtheadert2);
		imagettftext ($cardImage, 12, 0, 82, 158, $textColor, 'fonts/Merriweather-Italic.ttf', $txtheadert3);
		imagettftext ($cardImage, 10.5, 0, 82, 192, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert4);
		imagettftext ($cardImage, 12, 0, 117, 192, $textColor, 'fonts/MerriweatherSans-BoldItalic.ttf', $txtheadert5);
		imagettftext ($cardImage, 10.5, 0, 82, 220, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert6);
		imagettftext ($cardImage, 10.5, 0, 82, 252, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert7);
		voucherGenerator::imagettftextjustified($cardImage, 12.75, 0, 82, 252, $textColor, 'fonts/MerriweatherSans-Bold.ttf', $prizename, 289);	//instead of pass in the subtring of prizes
		//imagettftext ($cardImage, 12.75, 0, 87, 270, $textColor, 'fonts/MerriweatherSans-Bold.ttf', $txtheadert8);
		//imagettftext ($cardImage, 12.75, 0, 87, 290, $textColor, 'fonts/MerriweatherSans-Bold.ttf', $txtheadert9);
		//imagettftext ($cardImage, 12.75, 0, 87, 310, $textColor, 'fonts/MerriweatherSans-Bold.ttf', $txtheadert10);
		imagettftext ($cardImage, 10.5, 0, 82, 342, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert11);
		imagettftext ($cardImage, 15, 0, 82, 365, $textColor, 'fonts/MerriweatherSans-Bold.ttf', $txtheadert12);
		imagettftext ($cardImage, 7.5, 0, 168, 430, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert13);
		imagettftext ($cardImage, 7.5, 0, 237, 430, $textColor, 'fonts/MerriweatherSans-Bold.ttf', $txtheadert14);
		imagettftext ($cardImage, 7.5, 0, 425, 430, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert15);
		imagettftext ($cardImage, 7.5, 0, 208, 445, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert16);
		imagettftext ($cardImage, 7.5, 0, 186, 460, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert17);
		imagettftext ($cardImage, 7.5, 0, 82, 494, $textColor, 'fonts/MerriweatherSans-Bold.ttf', $txtheadert18);
		imagettftext ($cardImage, 7.5, 0, 82, 509, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert19);
		imagettftext ($cardImage, 7.5, 0, 82, 524, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert20);
		imagettftext ($cardImage, 7.5, 0, 82, 539, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert21);
		imagettftext ($cardImage, 7.5, 0, 82, 554, $textColor, 'fonts/MerriweatherSans-Light.ttf', $txtheadert22);
		//$weekImageString = 'cardimages/valid-week'.$week.'.png';
		//list($weekwidth, $weekheight) = getimagesize($weekImageString); 
		//$weekindexImage = imagecreatefrompng($weekImageString);
		//voucherGenerator::imagecopymerge_alpha($cardImage, $weekindexImage,(399-$weekwidth),(646-$weekheight),0,0,13,205,100,true);
		//$watermarkimage = imagecreatefrompng('cardimages/logo-watermark.png');
		//voucherGenerator::imagecopymerge_alpha($cardImage, $watermarkimage,23,231,0,0,354,338,100,true);
		/*
		$name = time();
		imagejpeg($cardImage, '_temp/'.$name.'.jpg',90);
		*/
		imagejpeg($cardImage, "evouchers/".$serial.".jpg", 90);
		ob_start(); // buffers future output
		imagejpeg($cardImage, NULL, 90); // writes to output/buffer
		$b64 = base64_encode(ob_get_contents()); // returns output
		ob_end_clean(); // clears buffered output
		imagedestroy($cardImage);
		return array("serial"=>$serial_number, "sequence"=>implode(",",$number), "data"=>$b64); 
	}
	
	private static function imagettftextjustified(&$image, $size, $angle, $left, $top, $color, $font, $text, $max_width, $minspacing=3,$linespacing=1)
	{
		$wordwidth = array();
		$linewidth = array();
		$linewordcount = array();
		$largest_line_height = 0;
		$lineno=0;
		$words=explode(" ",$text);
		$wln=0;
		$linewidth[$lineno]=0;
		$linewordcount[$lineno]=0;
		foreach ($words as $word)
		{
			$dimensions = imagettfbbox($size, $angle, $font, $word);
			$line_width = $dimensions[2] - $dimensions[0];
			$line_height = $dimensions[1] - $dimensions[7];
			if ($line_height>$largest_line_height) $largest_line_height=$line_height;
			if (($linewidth[$lineno]+$line_width+$minspacing)>$max_width)
			{
			$lineno++;
			$linewidth[$lineno]=0;
			$linewordcount[$lineno]=0;
			$wln=0;
			}
			$linewidth[$lineno]+=$line_width+$minspacing;
			$wordwidth[$lineno][$wln]=$line_width;
			$wordtext[$lineno][$wln]=$word;
			$linewordcount[$lineno]++;
			$wln++;
		}
		for ($ln=0;$ln<=$lineno;$ln++)
		{
			$slack=$max_width-$linewidth[$ln];
			if (($linewordcount[$ln]>1)&&($ln!=$lineno)) $spacing=($slack/($linewordcount[$ln]-1));else $spacing=$minspacing;
			$x=0;
			for ($w=0;$w<$linewordcount[$ln];$w++)
			{
				imagettftext($image, $size, $angle, $left + intval($x), $top + $largest_line_height + ($largest_line_height * $ln * $linespacing), $color, $font, $wordtext[$ln][$w]);
				$x+=$wordwidth[$ln][$w]+$spacing+$minspacing;
			}
		}
		return true;
	}
}
?>