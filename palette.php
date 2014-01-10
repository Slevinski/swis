<?php
/**
 * Symbol Palette Page
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

echo '<html><head>';
echo '<script src="js/jquery.js"></script>';
echo '<link href="css/palette.css" rel="stylesheet" type="text/css" media="all">';
$css = @$_REQUEST['css'];
if ($css){
  echo '<link href="' . $css . '.css" rel="stylesheet" type="text/css" media="all">';
}
$subset=@$_REQUEST['subset'];

echo '</head><body onload="loadPalette();">';
echo '<div id="command" class="command">';
echo '<center>';
echo '<div onclick="PaletteSetTop();"><img src="media/sg_list.png"></div>';
echo '<div onclick="PalettePrevious();"><img src="media/previous.png"></div>';
echo '</center>';
echo '</div>';
echo '<div id="palette" class="palette">';
echo '</div>';
echo '<script type="text/javascript" src="js/keyISWA.js"></script>';
echo '<script type="text/javascript" src="js/palette.js"></script>';
echo '</body></html>';
?>
