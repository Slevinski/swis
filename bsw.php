<?php
/**
 * Binary SignWriting Library
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
 * @brief Binary SignWriting encoding for symbols, numbers, and markers
 * @file
 *   
 */
 
//include 'filesystem.php';
include 'database.php';

/** @defgroup bsw Binary SignWriting
 *  Encoding for symbols, numbers, and markers
 */

/** @defgroup key Symbol Key
 *  @ingroup bsw
 *  Symbol key is a subsection of the ISWA
 */

/** @defgroup range Symbol Ranges
 *  @ingroup bsw
 *  Symbol Ranges is a subsection of the ISWA
 */

/** @defgroup str BSW Strings
 *  @ingroup bsw
 *  Binary SignWriting string functions
 */

/** @defgroup num Numbers
 *  @ingroup bsw
 *  Number functions
 */

/** 
 * @brief symbol base to key for viewing
 * @param $base symbol key prefix
 * @return a symbol key for viewing
 * @ingroup key
 */
function base2view($base){
  $view = $base . '00';
  if (isHand($base)){
    if(!isSymGrp($base)){
      $view = $base . '10';
    }
  }
  return $view;
}

/** 
 * @brief returns the symbol group for a base or key
 * @param $base symbol key prefix
 * @return the key prefix for a group
 * @ingroup key
 */
function base2group($base){
  $base = substr($base,0,3);
  $sg_list = array('100','10e','11e','144','14c','186','1a4','1ba','1cd','1f5', '205','216','22a','255','265','288','2a6','2b7','2d5','2e3', '2f7', '2ff','30a','32a','33b','359', '36d','376', '37f', '387');
  foreach ($sg_list as $group){
    if (hexdec($base)==hexdec($group)) return $group;
    if (hexdec($base)<hexdec($group)) return $prev;
    $prev = $group;
  }
  return $group;
}

/** 
 *  List of key prefixes of the 30 groups
 * @ingroup key
 */
$sg_list = array('100','10e','11e','144','14c','186','1a4','1ba','1cd','1f5', '205','216','22a','255','265','288','2a6','2b7','2d5','2e3', '2f7', '2ff','30a','32a','33b','359', '36d','376', '37f', '387');

/** 
 * @brief symbol base to key for viewing
 * @param $base symbol key prefix
 * @return boolean value if the key prefix is a group
 * @ingroup key
 */
function isSymGrp($char){
  $sg_list = array('100','10e','11e','144','14c','186','1a4','1ba','1cd','1f5', '205','216','22a','255','265','288','2a6','2b7','2d5','2e3', '2f7', '2ff','30a','32a','33b','359', '36d','376', '37f', '387');
  return in_array($char,$sg_list);
}


/** 
 *  List of key prefixes of the 7 categories
 * @ingroup key
 */
$cat_list = array('100','205','2f7', '2ff','36d','37f','387');

/** 
 * @brief returns the symbol category for a base or key
 * @param $base symbol key prefix
 * @return the key prefix for a category
 * @ingroup key
 */
function base2cat($base){
  $cat_list = array('100','205','2f7', '2ff','36d','37f','387');
  foreach ($cat_list as $cat){
    if (hexdec($base)==hexdec($cat)) return $cat;
    if (hexdec($base)<hexdec($cat)) return $prev;
    $prev = $cat;
  }
  return $cat;
}

/** 
 *  Global variable that contains BaseSymbol information
 * @ingroup key
 */
$BaseSymbols = array();

/** 
 * @brief test if key is properly formatted
 * @param $key symbol key
 * @return boolean value if the key is properly formatted
 * @ingroup key
 */
function isKey($key){
  if ($key=='') return false;
  $re_sym = 'S?[123][a-f0-9]{2}[012345][a-f0-9]';
  $re_pattern = '/^' . $re_sym . '$/i';

  $result = preg_match($re_pattern,$key,$matches);
  if ($result) {
    if ($key == $matches[0]) {
      return true;
    }
  }
  return false;
}

/** 
 * @brief test if key references a valid symbol
 * @param $key symbol key
 * @return boolean value if the key is a valid symbol
 * @ingroup key
 */
function validKey($key){
  if (!isKey($key)) return false;
  global $BaseSymbols;
  if (count($BaseSymbols)==0) $BaseSymbols = loadBaseSymbols();
  $key = str_replace("S","",$key);
  $len = strlen($key);
  if ($len<3){ return false ;}//error
  $hcode = substr($key,0,3);

  $bs = $BaseSymbols[hexdec($hcode)];
  if (!$bs){return false;}

  $df = 0;
  if ($len>3) $df = hexdec(substr($key,3,1));
  $dr = 0;
  if ($len>4) $dr = hexdec(substr($key,4,1));
  //now for binary algebra!
  $fillbin = pow(2,$df);
  $bFill = $fillbin & $bs['fills'];
  $rotbin = pow(2,$dr);
  $bRot = $rotbin & $bs['rots'];
  return $bFill and $bRot;
}

/** 
 * @brief returns the 16-bit symbol code of a key
 * @param $key symbol key
 * @return 16 bit symbol code
 * @ingroup key
 */
function key2code($key){
  $key = str_replace('S','',$key);
  return ((hexdec(substr($key,0,3)) - 256) * 96) + ((hexdec(substr($key,3,1)))*16) + hexdec(substr($key,4,1))+1;
}

/** 
 * @brief returns the symbol key of a 16-bit symbol code
 * @param $code 16-bit symbol code
 * @return symbol key
 * @ingroup key
 */
function code2key($code){
$ode = $code;
  $base = intval($code/96);
  $code = $code - $base*96;
  $fill = intval(($code-1)/16);
  $rot = $code - ($fill*16);
  if ($fill==0 && $rot==0) {
    $base--;
    $fill=5;
    $rot=16;
  }
  return dechex($base+256) . dechex($fill) . dechex($rot-1);
}

/** 
 * @brief test if symbol base is in range
 * @param $start start of hex range
 * @param $end end of hex range
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function inHexRange($start, $end, $char){
  $char = substr(str_replace('S','',$char),0,3);
  if (hexdec($char)>=hexdec($start) and hexdec($char)<=hexdec($end)){
    return true;
  } else {
    return false;
  }
}

/** 
 * @brief test if symbol base is in the ISWA
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function isISWA($char){
  return inHexRange("100","38b",$char); 
}

/** 
 * @brief test if symbol base is a writing symbol
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function isWrit($char){
  return inHexRange("100","37e",$char); 
}

/** 
 * @brief test if symbol base is a hand symbol
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function isHand($char){
  return inHexRange("100","204",$char); 
}

/** 
 * @brief test if symbol base is a movement symbol
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function isMove($char){
  return inHexRange("205","2f6",$char); 
}

/** 
 * @brief test if symbol base is a dynamics symbol
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function isDyn($char){
  return inHexRange("2f7","2fe",$char); 
}

/** 
 * @brief test if symbol base is a head symbol
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function isHead($char){
  return inHexRange("2ff","36c",$char); 
}

/** 
 * @brief test if symbol base is a head symbol
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function hasHead($text){
  $re_sym = 'S(2ff|3[0-5][0-9a-f]|36[0-9a-c])';
  $re_pattern = '/' . $re_sym . '/i';
  $result = preg_match_all($re_pattern,$text,$matches);
  return count($matches[0])>0;
}

/** 
 * @brief test if symbol base is a trunk symbol
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function isTrunk($char){
  return inHexRange("36d","375",$char); 
}

/** 
 * @brief test if symbol base is a limb symbol
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function isLimb($char){
  return inHexRange("376","37e",$char); 
}

/** 
 * @brief test if symbol base is a detailed location symbol
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function isLoc($char){
  return inHexRange("37f","386",$char); 
}

/** 
 * @brief test if symbol base is a punctuation symbol
 * @param $char hex string to test in range
 * @return boolean value
 * @ingroup range
 */
function isPunc($char){
  return inHexRange("387","38b",$char); 
} 

/** 
 * @brief returns the fill value of a bsw fill char
 * @param $char bsw string of a fill value
 * @return fill value
 * @ingroup str
 */
function char2fill($char){
  return dechex(hexdec($char)-hexdec('110'));
}

/** 
 * @brief returns the base fill char of a fill value
 * @param $fill fill value
 * @return bsw fill char
 * @ingroup str
 */
function fill2char($fill){
  return dechex(hexdec($fill)+hexdec('110'));
}

/** 
 * @brief returns the rotation value of a bsw rotation char
 * @param $char bsw string of a rotation value
 * @return rotation value
 * @ingroup str
 */
function char2rot($char){
  return dechex(hexdec($char)-hexdec('120'));
}

/** 
 * @brief returns the base rotation char of a rotation value
 * @param $rotation rotation value
 * @return bsw rotation char
 * @ingroup str
 */
function rot2char($rot){
  return dechex(hexdec($rot)+hexdec('120'));
}

/** 
 * @brief returns the bsw string of a symbol key
 * @param $key symbol key
 * @return bsw string
 * @ingroup str
 */
function key2bsw($key){
  $key = str_replace('S','',$key);
  $base = dechex(hexdec(substr($key,0,3)) + hexdec('30'));
  $fill = substr($key,3,1);
  $rot = substr($key,4,1);
  return $base . fill2char($fill) . rot2char($rot);
}

/** 
 * @brief returns the symbol key of a bsw string
 * @param $bsw bsw string
 * @return symbol key
 * @ingroup str
 */
function bsw2key($bsw){
  $bsw = str_replace(' ','',$bsw);
  $bsw = str_replace('_','',$bsw);
  $base = dechex(hexdec(substr($bsw,0,3)) - hexdec('30'));
  $fill = substr($bsw,3,3);
  $rot = substr($bsw,6,3);
  return $base . char2fill($fill) . char2rot($rot);
}


/** 
 * @brief test if text is Binary SignWriting 
 * @param $text character string
 * @return boolean value if text is Binary SignWriting with number characters
 * @ingroup str
 */
function bswText($text){
  $bsw_sym = '[123][0-9a-f]{2}11[0-5]12[0-9a-f]';
  $bsw_coord = '([78][0-9a-f]{2}){2}';
  $bsw_word = '(100(' . $bsw_sym. ')+)?10[1-4](' . $bsw_coord . ')(' . $bsw_sym . $bsw_coord . ')*';
  $bsw_punc = '3b[7-9ab]11[0-5]12[0-9a-f]' . $bsw_coord;
  $bsw_pattern = '/^(' . $bsw_word . '|' . $bsw_punc . ')( ' . $bsw_word . '| ' . $bsw_punc .')*$/i';

  $result = preg_match($bsw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return true;
    }
  }
  return false;
}

/** 
 * @brief x,y values to irregular coordinate string
 * @param $x X value
 * @param $y Y value
 * @return a coordinate string centered on 0x0
 * @ingroup num
 */
function koord2str($x,$y){
  $str = '';
  if ($x<0) $str .= 'n';
  $str .= abs($x);
  $str .= 'x';
  if ($y<0) $str .= 'n';
  $str .= abs($y);
  return $str;
}

/** 
 * @brief irregular coordinate string to array of x,y values
 * @param $str  coordinate string centered on 0x0
 * @return an array of x,y values 
 * @ingroup num
 */
function str2koord($str) {
  if (!$str) return array(0,0);
  $str = str_replace('n','-',$str);
  $parts = explode('x',$str);
  $coord = array();
  foreach ($parts as $part){
    $coord[] = $part;
  }
  return $coord;
} 

/** 
 * @brief x,y values to regular coordinate string
 * @param $x X value
 * @param $y Y value
 * @return a coordinate string centered on 500x500
 * @ingroup num
 */
function coord2str($x,$y){
  $str = '';
  $str .= $x + 500;
  $str .= 'x';
  $str .= $y + 500;
  return $str;
}

/** 
 * @brief regular coordinate string to array of x,y values
 * @param $str  coordinate string centered on 500x500
 * @return an array of x,y values 
 * @ingroup num
 */
function str2coord($str) {
  if (!$str) return array(0,0);
  $parts = explode('x',$str);
  $coord = array();
  foreach ($parts as $part){
    $coord[] = $part -500;
  }
  return $coord;
}

/** 
 * @brief number value to BSW character
 * @param $num number from -250 to 249
 * @return BSW character
 * @ingroup num
 */
function num2bsw($num) {
  return dechex($num + hexdec('800'));
}

/** 
 * @brief BSW character to number value
 * @param $bsw BSW Number character
 * @return number from -250 to 249
 * @ingroup num
 */
function bsw2num($bsw) {
  return hexdec($bsw) - hexdec('800');
}

/** 
 * @brief coordinate array to BSW string
 * @param $arr array of 0 based x,y values
 * @return BSW String
 * @ingroup num
 */
function coord2bsw($arr) {
  return num2bsw($arr[0]) . num2bsw($arr[1]);
}

/** 
 * @brief BSW string to coordinate array
 * @param $bsw string of 2 bsw numbers
 * @return coordinate array
 * @ingroup num
 */
function bsw2coord($bsw) {
  return array(bsw2num(substr($bsw,0,3)),bsw2num(substr($bsw,3,3)));
}

?>
