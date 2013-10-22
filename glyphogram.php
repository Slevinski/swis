<?php
/**
 * Glyphogram image
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
 * @copyright 2007-2012 Stephen E Slevinski Jr 
 * @author Steve Slevinski (slevin@signpuddle.net)  
 * @version 1
 * @section License 
 *   GPL 3, http://www.opensource.org/licenses/gpl-3.0.html
 * @brief Glyphogram image script
 * @file
 *   
 */

//stable names and font since Jan 12th, 2012 speed bump
if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] ||  $_SERVER['HTTP_IF_NONE_MATCH']) {
    header("HTTP/1.1 304 Not Modified");
    exit;
}

/**
 * include general libraries
 */
include 'image.php';
include 'msw.php';

/**
 * attributes
 */ 
$ksw = @$_REQUEST['ksw'];  //BSW 3 text, layout, or panel
$bsw = @$_REQUEST['bsw'];  //BSW 3 text, layout, or panel

$text = @$_REQUEST['text'];
$panel = @$_REQUEST['panel'];
$font= @$_REQUEST['font'];
$size = @$_REQUEST['size'];
$pad=@$_REQUEST['pad'];
$bound=@$_REQUEST['bound'];
$line = @$_REQUEST['line'];
$fill = @$_REQUEST['fill'];
$back = @$_REQUEST['back'];
$break = @$_REQUEST['break'];
$colorize = @$_REQUEST['colorize'];

if (fswText($text)) {
//  nothing
} else if (kswLayout($ksw)) {
  $text = $ksw;
} else if (kswPanel($text)){
  $cluster = panel2cluster($text);
  $text = cluster2ksw($cluster);
}
if (kswPanel($panel)) {
  $cluster = panel2cluster($panel);
  $text = cluster2ksw($cluster);
}

$name= @$_REQUEST['name'];
if(!$name){$name=$text;}

$fmt = substr($font,0,3);
$ver = substr($font,3,1);

$filename = 'img/' . image_name($font,$size,$pad,$bound,$colorize,$line,$fill,$back,$text,$break);
$etag = md5($filename);
$filehash = '';
if (strlen($text)>250){
  $filename = str_replace($text,$etag,$filename);
  $filehash = $filename;
}

//stable names and font since Jan 12th, 2012
$lastmod = 'Thu, 12 Jan 2012 16:20:01 GMT';
//check moved to start of script.  Uncomment if lastmod changes.
//if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastmod ||
//    trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
//    header("HTTP/1.1 304 Not Modified");
//    $logFile = str_replace('.' . $fmt, '.log',$filename);
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
    if (file_exists($filename) ){
      echo file_get_contents($filename);
    } else {
      if (fswText($text)) {
        $ksw = fsw2ksw($text);
        $ext = 'fsw';

      } else {
        $ksw = $text;
        $ext = 'ksw';
      }
      $contents = glyphogram_txt($ksw, $ver, $pad, $bound, $line, $fill, $back, $break);
      @mkdir(dirname($filename),0777,true);
      if ($filehash){
        $filehash = str_replace('.' . $fmt,'.' . $ext,$filehash);
        @file_put_contents($filehash,$text);
      }
      @file_put_contents($filename,$contents);
      echo $contents;
    }
    break;
  case "svg":
    header("Content-type: image/svg+xml");
    header('Content-Disposition: filename=' . $name . '.svg');
    if (file_exists($filename) ){
      echo file_get_contents($filename);
    } else {
      if (fswText($text)) {
        $ksw = fsw2ksw($text);
      } else {
        $ksw = $text;
      }
      $contents = glyphogram_svg($ksw, $ver, $size, $pad, $bound, $line, $fill, $back, $colorize);
      @mkdir(dirname($filename),0777,true);
      //no need for $filehash check because text written as metadata inside of SVG
      @file_put_contents($filename,$contents);
      echo $contents;
    }
    break;
  default://png
    header("Content-type: image/png");
    header('Content-Disposition: filename=' . $name . '.png');
    if (file_exists($filename) ){
      echo file_get_contents($filename);
    } else {
      if (fswText($text)) {
        $ksw = fsw2ksw($text);
        $ext = 'fsw';
      } else {
        $ksw = $text;
        $ext = 'ksw';
      }
      $contents = glyphogram_png($ksw, $ver, $size, $pad, $bound, $line, $fill, $back, $colorize);
      @mkdir(dirname($filename),0777,true);
      if ($filehash){
        $filehash = str_replace('.' . $fmt,'.' . $ext,$filehash);
        @file_put_contents($filehash,$text);
      }
      @ImagePNG($contents,$filename);
      ImagePNG($contents);
    }
}
?>
