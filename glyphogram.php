<?php
/**
 * Glyphogram image
 * 
 * Copyright 2007-2010 Stephen E Slevinski Jr
 * Steve (Slevin@signpuddle.net)
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
 * @copyright 2007-2010 Stephen E Slevinski Jr 
 * @author Steve (slevin@signpuddle.net)  
 * @license http://www.opensource.org/licenses/gpl-3.0.html GPL
 * @access public
 * @package SWIS
 * @version 1.2.0
 * @filesource
 *   
 */

/**
 * include general libraries
 */
include 'csw.php';
include 'image.php';

/**
 * attributes
 */ 
$ksw = @$_REQUEST['ksw'];  //BSW 3 text, layout, or display
$bsw = @$_REQUEST['bsw'];  //BSW 3 text, layout, or display
$bsw2 = @$_REQUEST['bsw2'];  //BSW 2010
$bsw3 = @$_REQUEST['bsw3'];  //BSW 3 legacy

$text = @$_REQUEST['text'];
$display = @$_REQUEST['display'];
$style = @$_REQUEST['style'];
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

if ($bsw2){
  $bsw3 = bsw2bsw3($bsw2);
}
if ($bsw3) {
  $bsw3c = new BSW_3C();
  $bsw = $bsw3c->convert($bsw3);
}

if (cswLayout($text)) {
  $ksw = csw2ksw($text);
}

if (kswLayout($text)) {
  $ksw = $text;
}
if (kswDisplay($display)) {
  $cluster = display2cluster($display);
  $ksw = cluster2ksw($cluster);
}
//display
$fmt = substr($style,0,3);
$ver = substr($style,3,1);
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
