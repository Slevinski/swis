<?php
/**
 * Column image for sign text
 * 
 * Copyright 2007-2011 Stephen E Slevinski Jr
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
 * @copyright 2007-2011 Stephen E Slevinski Jr 
 * @author Steve (slevin@signpuddle.net)  
 * @license http://www.opensource.org/licenses/gpl-3.0.html GPL
 * @access public
 * @package SWIS
 * @version 2.0
 * @filesource
 *   
 */

/**
 * include, attributes, and header
 */ 
include 'csw.php';

$req = array('ksw','length','size','color','colorize','background','style');
foreach ($req as $name){
  $$name = @$_REQUEST[$name];
}
if (!$style) $style='png';

$lst = array('width','signTop','signBottom','padding','puncBottom','offset','justify');
$params = array();
foreach ($lst as $param){
  $value = @$_REQUEST[$param];
  if ($value!="") {
    $params[$param] = @$_REQUEST[$param];
  }
}
if ($params['width'] && $size){
  $params['width'] = intval($params['width']/$size);
}

echo '<html><head>';
echo '<link href="columns.css" rel="stylesheet" type="text/css" media="all">';
echo '</head><body>';
if ($length=='') $length=400;

$ksw = trim($ksw);
$display = ksw2display($ksw,intval($length/$size),$params);
$cnt = count($display);
$fmt = substr($style,0,3);
switch ($fmt){
case "png":
  $pre = '<div class="signtextcolumn"><img src="glyphogram.php?style=' . $style . '&size=' . $size;
  if ($color) $pre .= '&line=' . $color;
  if ($colorize) $pre .= '&colorize=1';
  if ($background) {
    $pre .= '&back=' . $background;
    $pre .= '&fill=' . $background;
  }
  if ($color || $background) $style="png1";
  forEach($display as $col){
    if ($cnt==1){
      $col = displayTrim($col);
      echo $pre . '&ksw=' . $col . '"></div>';
    } else {
      echo $pre . '&display=' . $col . '"></div>';
    }
  }
  break;
case "txt":
  include 'image.php';
  $pre = '<div class="signtextcolumn"><tt>';
  forEach($display as $col){
    $cluster = display2cluster($col);
    $ksw = cluster2ksw($cluster);
    echo $pre . str_replace("\n","<br>",str_replace(' ','&nbsp;',glyphogram_txt($ksw)))  . '</tt></div>';
  }
  break;
case "svg":
  $pre = '<div class="signtextcolumn">';
  forEach($display as $col){
    $cluster = display2cluster($col);
    $ksw = cluster2ksw($cluster);
    $max = $cluster[0][1];
    $coord = str2coord($max);
    $wsvg = ceil($coord[0]*$size);
    $hsvg = ceil($coord[1]*$size);
    echo $pre . '<embed type="image/svg+xml" width="' . $wsvg . '" '; 
    echo 'height="' . $hsvg . '" src="' . $host . 'glyphogram.php?style=' . $style . '&';
    if ($size!=1) echo 'size=' . $size . '&';
    if ($color) echo 'line=' . $color . '&';
    if ($colorize) echo 'colorize=1&';
    if ($background) echo 'back=' . $background . '&';
    echo 'text=' . $ksw . '" ';
    echo 'pluginspage="http://www.adobe.com/svg/viewer/install/" style="overflow:hidden">';
    echo '</embed></div>';
  }
  break;
}
echo '<br clear="all">';
echo '</body></html>';
?>
