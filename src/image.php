<?php

/* 
 * Generate and output rescale image
 * 
 */
define("X_RESOLUTION", 1000);


function rescaleImage($name, $x = X_RESOLUTION, $newName = null ) {
       
    if (exif_imagetype($name) === IMAGETYPE_JPEG) {
        /*  Rescale  jpeg image   */
        $image = imagecreatefromjpeg($name);
        $image = imagescale($image, $x);

    } elseif (exif_imagetype($name) === IMAGETYPE_PNG) {
       
        $image = imagecreatefrompng($name);
        $image = imagescale($image, $x);
        
    } elseif (exif_imagetype($name) === IMAGETYPE_GIF) {
        
        $image = imagecreatefromgif($name);
        $image = imagescale($image, $x);  
        
    } elseif (exif_imagetype($name) === IMAGETYPE_BMP) {
        
        $image = imagecreatefrombmp($name);
        $image = imagescale($image, $x);
        
    } else {
        /*  Make custom description preview in case not image file */
        // Create a blank image and add some text
        $image = imagecreatetruecolor(100, 100);
        //$bg = imagecolorallocate($image, 255, 255, 0);
        $textColor = imagecolorallocate($image, 255, 0, 0);
        
        $extensions = strrchr($name, '.');
        imagestring($image, 5, 25, 40,  $extensions, $textColor);
    }
    
    if(!$newName) {
			header('Content-Type: image/jpeg');
			imagejpeg($image);
    } else {
			imagejpeg($image, $newName);
    }
    
    imagedestroy($image);
}

function getPhotoInfo($file, $path) {

    $fullName = $path.DIRECTORY_SEPARATOR.$file;
    $headers = exif_read_data($fullName, $sections = 'IFD0', $arrays = TRUE);

    if ($headers) {
        foreach ($headers['IFD0'] as $key => $value) {
            $meta[$key] = $value;
            /*  TODO: SAVE EXIF data    */
        }
        
    } else {
        /*	No EXIF header	*/
    }
   
}
?>
