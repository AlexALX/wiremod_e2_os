<?php
/*
	Created by AlexALX (c) 2018
	Converts any image to BMP file
	Also resizes to 512x512 and flip row order (rendering from top to bottom)
	For use with Expression2 Chip "BMP Reader"
*/

// you can reduce that values for faster loading
// maximum is 512 - digital screen limitation
$max_width = 512;
$max_height = 512;

ini_set("display_errors",0);

$url = $_SERVER['QUERY_STRING'];

if (empty($url)) die();

function get($url)
{
	if( $curl = curl_init())
	{
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER,         0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT,        5);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.1) Gecko/20090624 Firefox/3.5 (.NET CLR 3.5.30729)');
		$out = curl_exec($curl);
		curl_close($curl);
		return $out;
	}
}

// php < 5.4
if (!function_exists('getimagesizefromstring')) {
      function getimagesizefromstring($string_data)
      {
         $uri = 'data://application/octet-stream;base64,'  . base64_encode($string_data);
         return getimagesize($uri);
      }
}

$img_raw = get($url);
if (empty($img_raw)) die();

$size = getimagesizefromstring($img_raw);

if (count($size)) {

$img = imagecreatefromstring ($img_raw);
if (!$img) die();

// Digital Screen limitation
if ($max_width>512) $max_width = 512;
if ($max_height>512) $max_height = 512;

if ($size[0]>$max_width || $size[1]>$max_height) {
	$width = $max_width;
	$height = $max_height;
	$ratio_orig = $size[0]/$size[1];

	if ($width/$height > $ratio_orig) {
	   $width = $height*$ratio_orig;
	} else {
	   $height = $width/$ratio_orig;
	}
}

$dst_img = ImageCreateTrueColor($width,$height);
imagecopyresampled($dst_img,$img,0,0,0,0,$width,$height,$size[0],$size[1]);

header("Content-Type: image/bmp");

// use own function to flip row order
// taken from https://github.com/wapmorgan/Imagery/blob/master/src/function.imagebmp.php
// MIT License
function imagebmp_flip(&$img, $filename = false) {
    $wid = imagesx($img);
    $hei = imagesy($img);
    $wid_pad = str_pad('', $wid % 4, "\0");
    $size = 54 + ($wid + $wid_pad) * $hei;
    //prepare & save header
    $header['identifier'] = 'BM';
    $header['file_size'] = dword($size);
    $header['reserved'] = dword(0);
    $header['bitmap_data'] = dword(54);
    $header['header_size'] = dword(40);
    $header['width'] = dword($wid);
    $header['height'] = dword($hei*-1);
    $header['planes'] = word(1);
    $header['bits_per_pixel']= word(24);
    $header['compression']= dword(0);
    $header['data_size'] = dword(0);
    $header['h_resolution'] = dword(0);
    $header['v_resolution'] = dword(0);
    $header['colors'] = dword(0);
    $header['important_colors'] = dword(0);
    if ($filename) {
        $f = fopen($filename, "wb");
        foreach ($header AS $h) {
            fwrite($f, $h);
        }
        //save pixels
        for ($y = 0; $y <= $hei - 1; $y++) {
            for ($x=0; $x<$wid; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                fwrite($f, byte3($rgb));
            }
            fwrite($f, $wid_pad);
        }
        return fclose($f);
    }
    else {
        foreach ($header AS $h) {
            echo $h;
        }
        //save pixels
        for ($y = 0; $y <= $hei - 1; $y++) {
            for ($x=0; $x<$wid; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                echo byte3($rgb);
            }
            echo $wid_pad;
        }
        return true;
    }
}
function byte3($n) {
    return chr($n & 255) . chr(($n >> 8) & 255) . chr(($n >> 16) & 255);
}
function dword($n) {
    return pack("V", $n);
}
function word($n) {
    return pack("v", $n);
}

echo imagebmp_flip($dst_img);

}

?>