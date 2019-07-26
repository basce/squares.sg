<?php
 
function thumb_crop($src_image, $dest_image, $thumbwidth = 64, $thumbheight = 64, $jpg_quality = 90, $crop=true, $rotate=0) {
 
    // Get dimensions of existing image
    $image = getimagesize($src_image);
 
    // Check for valid dimensions
    if( $image[0] <= 0 || $image[1] <= 0 ) return false;
 
    // Determine format from MIME-Type
    $image['format'] = strtolower(preg_replace('/^.*?\//', '', $image['mime']));
 
    // Import image
    switch( $image['format'] ) {
        case 'jpg':
        case 'jpeg':
            $image_data = imagecreatefromjpeg($src_image);
        break;
        case 'png':
            $image_data = imagecreatefrompng($src_image);
        break;
        case 'gif':
            $image_data = imagecreatefromgif($src_image);
        break;
        default:
            // Unsupported format
            return false;
        break;
    }
 
    // Verify import
    if( $image_data == false ) return false;
	
	imageinterlace($image_data, true); //progressive image
 
    // Calculate measurements
    if( $image[0] / $image[1] > $thumbwidth / $thumbheight ) {
        // For landscape images
		$scale =  $image[1] / $thumbheight;
		if($crop){
			$x_offset = ($image[0] - $scale * $thumbwidth) / 2;
			$y_offset = 0;
			$width_size = $image[0] - ($x_offset * 2);
			$height_size = $image[1];
		}else{
			$x_offset = 0;
			$y_offset = 0;
			$width_size = $image[0];
			$height_size = $image[1];
			$thumbheight = $thumbwidth * $image[1] / $image[0];
		}
    } else {
        // For portrait and square images
		$scale =  $image[0] / $thumbwidth;
        if($crop){
			$x_offset = 0;
			$y_offset = ($image[1] - $scale * $thumbheight) / 2;
			$width_size = $image[0];
			$height_size = $image[1] - ($y_offset * 2);
		}else{
			$x_offset = 0;
			$y_offset = 0;
			$width_size = $image[0];
			$height_size = $image[1];
			$thumbwidth = $thumbheight * $image[0] / $image[1];
		}
    }
 
    // Resize and crop
    $canvas = imagecreatetruecolor($thumbwidth, $thumbheight);
    if( imagecopyresampled(
        $canvas,
        $image_data,
        0,
        0,
        $x_offset,
        $y_offset,
        $thumbwidth,
        $thumbheight,
        $width_size,
        $height_size
    )) {
 
       //rotate if needed
	   if($rotate!=0){
		   $canvas = imagerotate($canvas, $rotate, 0);
	   }
	   
	    // Create thumbnail
        switch( strtolower(preg_replace('/^.*\./', '', $dest_image)) ) {
            case 'jpg':
            case 'jpeg':
                return imagejpeg($canvas, $dest_image, $jpg_quality);
            break;
            case 'png':
                return imagepng($canvas, $dest_image);
            break;
            case 'gif':
                return imagegif($canvas, $dest_image);
            break;
            default:
                // Unsupported format
                return false;
            break;
        }
 
    } else {
        return false;
    }
 
}
 
?>