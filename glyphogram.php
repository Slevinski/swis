<?php
/**
 * Glyphogram image
 * 
 * This file is part of SWIS: the SignWriting Image Server.
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
 * @version 3
 * @section License 
 *   GPL 3, http://www.opensource.org/licenses/gpl-3.0.html
 * @brief Glyphogram image script
 * @file
 *   
 */

/**
 * include general libraries
 */
include 'msw.php';
include 'image.php';

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
$name= @$_REQUEST['name'];
if(!$name){$name='glyphogram';}

if (fswText($text)) {
  $ksw = fsw2ksw($text);
} else if (kswLayout($text)) {
  $ksw = $text;
}
if (kswPanel($panel)) {
  $cluster = panel2cluster($panel);
  $ksw = cluster2ksw($cluster);
}

$fmt = substr($font,0,3);
$ver = substr($font,3,1);
switch ($fmt){
  case "txt":
    header("Content-type: text/plain");
    header('Content-Disposition: filename=' . $name . '.txt');
    echo glyphogram_txt($ksw, $ver, $pad, $bound, $line, $fill, $back, $break);
    break;
  case "svg":
    header("Content-type: image/svg+xml");
    header('Content-Disposition: filename=' . $name . '.svg');
    echo glyphogram_svg($ksw, $ver, $size, $pad, $bound, $line, $fill, $back, $colorize);
    break;
  default://png
    header("Content-type: image/png");
    header('Content-Disposition: filename=' . $name . '.png');
    ImagePNG(glyphogram_png($ksw, $ver, $size, $pad, $bound, $line, $fill, $back, $colorize));
}
?>
