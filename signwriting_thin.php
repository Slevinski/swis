<?php
  header("Content-type: text/javascript");
  header('Content-Disposition: filename="signwriting_thin.js"');
  include("edition.php");
?>
/**
 * Universal SignWriting Plugin, thin viewer
 *
 * Installation
 *   add this file to any web site or add these 2 lines to any web page
 
 * Copyright 2007-2013 Stephen E Slevinski Jr
 * Steve (Slevin@signpuddle.net)
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
 * @author Steve (slevin@signpuddle.net)
 * @license http://www.opensource.org/licenses/gpl-3.0.html GPL
 * @access public
 * @version 1.0.0.rc
 * @filesource
 *
 */

/**
 * Line 1
 *   first, define a function for regular expression search and replace
 *   second, crawl the document object model for TEXT elements with matching strings and apply the function
 */
<?php
echo $script . "\n";
?>

/**
 * Line 2
 *   execute function when DOM is loaded
 */
<?php
echo $autostart . "\n";
?>