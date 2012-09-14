<?php
/**
 * Character SignWrting Library
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
 * @brief Character SignWriting encoding with Unicode characters
 * @file
 *   
 */

/** @defgroup csw Character SignWriting
 *  Unicode character encoding for SignWriting
 */

/** @defgroup uni Unicode 
 *  @ingroup csw
 *  Common Unicode functions
 */

/** @defgroup unum Numbers
 *  @ingroup csw
 *  Unicode Number characters
 */

/** @defgroup ure Regular Expressions
 *  @ingroup csw
 *  Regular Expressions for Unicode characters
 */

/** 
 * @brief returns UTF-8 character for code
 * @param $code decimal value of code point
 * @param $plane plane for character
 * @return UTF-8 value
 * @ingroup uni
 */
function dec2utf($code,$plane=15){
  $a = $code%64;
  $b = floor($code/64);
  $c = floor($b/64);
  $b -= $c*64;
  
  switch($plane){
  case 1:
    $utf8 = "f0";
    $utf8 .= dechex($c + 144);//90
    $utf8 .= dechex($b + 128);//80
    $utf8 .= dechex($a + 128);//80
    break;
  case 15:
    $utf8 = "f3";
    $utf8 .= dechex($c + 176);//B0
    $utf8 .= dechex($b + 128);//80
    $utf8 .= dechex($a + 128);//80
    break;
  case 16:
    $utf8 = "f4";
    $utf8 .= dechex($c + 128);//80
    $utf8 .= dechex($b + 128);//80
    $utf8 .= dechex($a + 128);//80
    break;
  }

  return pack("N",hexdec($utf8));
}

/** 
 * @brief returns UTF-8 character for bsw character
 * @param $char bsw string character
 * @param $plane plane for character
 * @return UTF-8 value
 * @ingroup uni
 */
function char2utf($char,$plane=15){
  $code = hexdec($char)+55040;//primary shift
  return dec2utf($code,$plane);
}

/** 
 * @brief returns Unicode character description for bsw character
 * @param $char bsw string character
 * @param $plane plane for character
 * @return UTF-8 value
 * @ingroup uni
 */
function char2unicode($char,$plane=15){
  $code = hexdec($char)+55040;//primary shift
  return strtoupper(dechex($plane) . dechex($code));
}

/** 
 * @brief returns UTF-8 string for bsw string
 * @param $bsw bsw string
 * @param $plane plane for characters
 * @return UTF-8 string
 * @ingroup uni
 */
function bsw2csw($bsw, $plane=15){
  $parts = explode(' ',$bsw);
  $out = array();
  foreach ($parts as $part){
    $csw = '';
    $chars = str_split($part,3);
    forEach($chars as $char){
      $csw .= char2utf($char,$plane);
    }
    $out[] = $csw;
  }
  return implode($out,' ');
}

/** 
 * @brief returns bsw character for UTF-8 character
 * @param $utf UTF-8 charcter
 * @return bsw string char
 * @ingroup uni
 * @see csw2bsw
 */
function utf2char($utf){
  $val = unpack("N",$utf);
  $val = dechex($val[1]);
  $chars = str_split($val,2);
  $plane = $chars[0];
  switch($plane){
    case "f0":
      $a = hexdec($chars[1])-144;
      $b = hexdec($chars[2])-128;
      $c = hexdec($chars[3])-128;
      break;
    case "f3":
      $a = hexdec($chars[1])-176;
      $b = hexdec($chars[2])-128;
      $c = hexdec($chars[3])-128;
      break;
    case "f4":
      $a = hexdec($chars[1])-128;
      $b = hexdec($chars[2])-128;
      $c = hexdec($chars[3])-128;
      break;
    default:
      return "plane $plane";
  }

  $code = $c + $b*64 + $a * 64 * 64 - 55040;
  return dechex($code);
}

/** 
 * @brief returns bsw string for UTF-8 string
 * @param $bsw_utf UTF-8 string
 * @return bsw string
 * @ingroup uni
 * @see utf2char
 */
function csw2bsw($csw){
  $parts = explode(' ',$csw);
  $out = array();
  foreach ($parts as $csw){
    $bsw = '';
    $pattern ='/[\x{FD800}-\x{FDFF9}]/u';
    preg_match_all($pattern, $csw,$matches);
    forEach ($matches[0] as $uchar){
      $bsw = $bsw . utf2char($uchar);
    }
    $out[] = $bsw;
  }
  return implode($out,' ');
}

/** 
 * @brief number value to UTF-8
 * @param $num number from -250 to 249
 * @return UTF-8 character
 * @ingroup unum
 */
function num2utf($num) {
  return char2utf(dechex($num + hexdec('800')));
}

/** 
 * @brief coordinate array to UTF-8 string
 * @param $arr array of 0 based x,y values
 * @return UTF-8 characters
 * @ingroup unum
 */
function coord2utf($arr) {
  return num2utf($arr[0]) . num2utf($arr[1]);
}

/** 
 * @brief test if text is character SignWriting on Unicode PUA plane 15
 * @param $text character string
 * @return boolean value if text is SignWriting with Unicode
 * @ingroup ure
 */
function cswText($text){
  $csw_sym = '[\x{FD830}-\x{FDABB}][\x{FD810}-\x{FD815}][\x{FD820}-\x{FD82F}]';
  $csw_num = '[\x{FDE00}-\x{FDFFF}]';
  $csw_coord = $csw_num . $csw_num;
  $csw_word = '(\x{FD800}(' . $csw_sym. ')+)?[\x{FD801}-\x{FD804}](' . $csw_coord . ')(' . $csw_sym . $csw_coord . ')*';
  $csw_punc = $csw_sym . $csw_coord;
  $csw_pattern = '/^(' . $csw_word . '|' . $csw_punc . ')( ' . $csw_word . '| ' . $csw_punc .')*$/u';

  $result = preg_match($csw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return true;
    }
  }
  return false;
}


?>
