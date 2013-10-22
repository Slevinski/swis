<?php
/**
 * Glyph image
 * 
 * This file is part of SWIS: the SignWriting Icon Server.
 * 
 * SWIS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * SWIS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with SWIS.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * END Copyright
 *  
 * @copyright 2007-2013 Stephen E Slevinski Jr 
 * @author Steve Slevinski (slevin@signpuddle.net)  
 * @version 1
 * @section License 
 *   GPL 3, http://www.opensource.org/licenses/gpl-3.0.html
 * @brief Glyph image script
 * @file
 *   
 */

//stable font since Sept 19th, 2011 speed bump
if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] ||  $_SERVER['HTTP_IF_NONE_MATCH']) {
    header("HTTP/1.1 304 Not Modified");
    exit;
}

/**
 * include general iswa library
 */
include 'bsw.php';
include 'csw.php';
include 'spl.php';
include 'image.php';

/**
 * attributes
 */ 
$key = @$_REQUEST['key'];
$code = @$_REQUEST['code'];
$bsw = @$_REQUEST['bsw'];
$sym = @$_REQUEST['sym'];
if($sym){
  $key = bsw2key(csw2bsw($sym));
//  echo urlencode($sym) . ' and ' . $key;
}
$font= @$_REQUEST['font'];
if (!$font) $font='png1';

$size = @$_REQUEST['size'];//doesn't work for SVG
$line = @$_REQUEST['line'];
$fill = @$_REQUEST['fill'];
$back = @$_REQUEST['back'];//doesn't work
$break = @$_REQUEST['break'];//specialty for txt font
$colorize = @$_REQUEST['colorize'];

//testing and setting
if ($code){
  //determine BaseSymbol
  $base = intval(($code-1)/96)*96 + 1;
  $offset = $code-$base;
  $drot = $offset % 16;
  $dfill = ($offset-$drot)/16;
  $base = dechex(intval(($code-1)/96) + 256);
  $key = $base . dechex($dfill) . dechex($drot);
} else if ($key){
  $base = substr($key,0,3);
} else if ($bsw){
  $base = substr($bsw,0,3);
  if (strlen($bsw)>3){
    //$hfill = char2fill(substr($bsw,3,3));
    //$hrot = char2rot(substr($bsw,6,3));
    $key = bsw2key($bsw);
    //$base . $hfill . $hrot;
  } else {
    $key = base2view($base);
  }
} else {
  die();
}

$name= @$_REQUEST['name'];
if(!$name){$name=$key;}

$fmt = substr($font,0,3);
$ver = substr($font,3,1);

$filename = 'img/' . image_name($font,$size,$pad,$bound,$colorize,$line,$fill,$back,$key,$break);
$etag = md5($filename);

//stable font since Sept 19th, 2011 speed bump
$lastmod = 'Mon, 19 Sep 2011 16:20:03 GMT';
//check moved to start of script.  Uncomment if lastmod changes.
//if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastmod ||
//    trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
//    header("HTTP/1.1 304 Not Modified");
//    exit;
//}

$expires = 60*60*24*365;
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
header('Last-Modified: ' . $lastmod);
header("Etag: $etag");
switch ($fmt){
  case "txt":
    header("Content-type: text/plain");
    header('Content-Disposition: filename=' . $name . '.txt');
    echo glyph_txt($key, $ver, $line, $fill, $back, $break);
    break;
  case "svg":
    header("Content-type: image/svg+xml");
    header('Content-Disposition: filename=' . $name . '.svg');
    echo glyph_svg($key, $ver, $size, $line, $fill, $back, $colorize);
    break;
  default://png
    header("Content-type: image/png");
    header('Content-Disposition: filename=' . $name . '.png');
    ImagePNG(glyph_png($key,$ver,$size,$line, $fill, $back, $colorize));
}
?>