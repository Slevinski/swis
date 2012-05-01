<?php
/**
 * Modern SignWrting library
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
 * @brief Modern SignWriting script encoding library 
 * @file
 *   
 */

/** @defgroup msw MSW
 *  Modern SignWriting
 */

/** @defgroup gen General
 *  @ingroup msw
 *  General functions
 */

/** @defgroup conv Conversions
 *  @ingroup msw
 *  Conversion functions
 */

include('bsw.php');
include('csw.php');
include ('ksw.php');
include ('fsw.php');

/** 
 * @brief test if text uses lanes
 * @param $text ksw or fsw with signbox or lanes
 * @return boolean value if text uses lanes
 * @ingroup gen
 */
 function isVert ($text){
  $lp = strpos($text, "L");
  $mp = strpos($text, "M");
  $rp = strpos($text, "R");
  return $lp!==false || $mp!==false || $rp!==false;
}

/** 
 * @brief test if key references a valid symbol
 * @param $key symbol key
 * @return boolean value if the key is a valid symbol
 * @ingroup gen
 */
function reorient ($text){
  if (isVert($text)){
    $adj = 2;
    $text = str_replace ('L','B',$text);
    $text = str_replace ('M','B',$text);
    $text = str_replace ('R','B',$text);
  } else {
    $adj = -2;
    $text = str_replace ('B','M',$text);
  }

  $re_sym = 'S38[7-9ab][0-5u][0-9a-f]n[0-9]+xn[0-9]+';
  $re_pattern = '/' . $re_sym . '/i';

  $result = preg_match_all($re_pattern,$text,$matches);
  $puncs = array_unique($matches[0]);

  foreach ($puncs as $punc){
    $rot = $punc[5] + $adj;
    if ($rot>7) $rot -= 8;       
    if ($rot<0) $rot += 8;
    $rotated = substr($punc,0,5) . $rot;
    $text = str_replace ($punc,raw2ksw($rotated),$text);
  }
  return $text;  
}

/** 
 * @brief convert ksw to fsw
 * @param $ksw input ksw layout string
 * @return fsw text
 * @ingroup conv
 */
function ksw2fsw($ksw){
  //match all symbols...
  $segments = explode(' ',$ksw);
  $segsout = array();
  foreach ($segments as $ksw){
    $pattern = '/([BLMR]|S[123][0-9a-f]{2}[0-5][0-9a-f])n?[0-9]+xn?[0-9]+/i';
    preg_match_all($pattern,$ksw, $matches);
    $input = '';
    $output = '';
    foreach($matches[0] as $spatial){
      $len = strlen($spatial);
      $first = $spatial[0];
      if (strpos("BLMR",$first)===false){
        $key = substr($spatial,0,6);
      } else {
        $key = $first;
      }
      $klen = strlen($key);
      $str = substr($spatial,$klen,$len-$klen);
      $coord = str2koord($str);
      $input .= $spatial;
      $output .= $key . ($coord[0]+500) . 'x' . ($coord[1]+500);
    }
    $ksw = str_replace($input,$output,$ksw);
    $segsout[] = $ksw;
  }
  return implode(' ',$segsout);
}

/** 
 * @brief convert fsw to ksw
 * @param $fsw input fsw string
 * @return ksw text
 * @ingroup conv
 */
function fsw2ksw($fsw){
  $segments = explode(' ',$fsw);
  $segsout = array();
  $pattern = '/([BLMR]|S[123][0-9a-f]{2}[0-5][0-9a-f])[0-9]{3}x[0-9]{3}/i';
  foreach ($segments as $fsw){
    $input = '';
    $output = '';
    preg_match_all($pattern,$fsw, $matches);
    foreach($matches[0] as $str){
      $len = strlen($str);
      $pre = substr($str,0,$len-7);
      $coord = str2koord(substr($str,$len-7,7));
      $coord[0] -= 500;
      $coord[1] -= 500;
      $input .= $str;
      $output .= $pre . koord2str($coord[0],$coord[1]);
    }
    $segsout[] = str_replace($input,$output,$fsw);

  }
  return implode(' ',$segsout);
}

/** 
 * @brief convert ksw to fsw
 * @param $ksw input ksw layout string
 * @return fsw text
 * @ingroup conv
 */
function bsw2fsw($bsw){

  $words = array();
  //match each bsw word...
  $pattern = '/([1-8][0-9a-f]{2})+/i';
  preg_match_all($pattern,$bsw, $matches);
  foreach($matches[0] as $bword){
    $fsw = '';
    //match each bsw char...
    $pattern = '/([1-8][0-9a-f]{2})/i';
    preg_match_all($pattern,$bword, $chars);
    $coord = '';
    foreach($chars[0] as $char){
      if (inHexRange('100','104',$char)){
        switch($char){
          case '100':
            $fsw .= "A";
            break;
          case '101':
            $fsw .= "B";
            break;
          case '102':
            $fsw .= "L";
            break;
          case '103':
            $fsw .= "M";
            break;
          case '104':
            $fsw .= "R";
            break;
        }
      } else if (inHexRange('110','115',$char)){
        $fsw .= substr($char,2,1);
      } else if (inHexRange('120','12f',$char)){
        $fsw .= substr($char,2,1);
      } else if (inHexRange('130','3bb',$char)){
        $fsw .= 'S' . dechex(hexdec($char) - hexdec('30'));
      } else if (inHexRange('706','8f9',$char)){
        if ($coord){
          $coord .= $char;
          $coord = bsw2coord($coord);
          $fsw .= ($coord[0]+500) . 'x' . ($coord[1]+500);
          $coord = '';
        } else {
          $coord = $char;
        }
      }      
    }
    $words[] = $fsw;
  }

  $fsw = implode($words,' ');
  return $fsw;
}

/** 
 * @brief convert fsw to ksw
 * @param $fsw input fsw string
 * @return ksw text
 * @ingroup conv
 */
function fsw2bsw($fsw){
  $pattern = '/[0-9]{3}x[0-9]{3}/i';
  preg_match_all($pattern,$fsw, $matches);
  foreach($matches[0] as $str){
    $coord = str2koord($str);
    $coord[0] -= 500;
    $coord[1] -= 500;
    $fsw = str_replace($str,num2bsw($coord[0]) . num2bsw($coord[1]) ,$fsw);
  }

  $pattern = '/S[123][0-9a-f]{2}[0-5][0-9a-f]/i';
  preg_match_all($pattern,$fsw, $matches);
  foreach($matches[0] as $key){
    $fsw = str_replace($key,key2bsw($key),$fsw);
  }

  $fsw = str_replace('A','100',$fsw);
  $fsw = str_replace('B','101',$fsw);
  $fsw = str_replace('L','102',$fsw);
  $fsw = str_replace('M','103',$fsw);
  $fsw = str_replace('R','104',$fsw);

  return $fsw;
}

/** 
 * @brief convert fsw query to ksw
 * @param $qsearch fsw query
 * @return ksw text
 * @ingroup conv
 */
function query2ksw($qsearch){
  if (!fswQuery($qsearch)) return;
  $fsw_sym = 'S[123][0-9a-f]{2}[0-5u][0-9a-fu]';
  $fsw_coord = '[0-9]{3}x[0-9]{3}';
  $fsw_query = $fsw_sym . $fsw_coord;
  $fsw_pattern = '/' . $fsw_query . '/i';

  $result = preg_match_all($fsw_pattern,$qsearch,$matches);
  $raw = 'M';
  $minX = 0;
  $minY = 0;
  $symsearch='';
  foreach ($matches[0] as $part){
    $iswa = '';
    $base = substr($part,1,3);
    $fill = substr($part,4,1);
    $rotate = substr($part,5,1);
    $symsearch .= 'S' . $base . $fill . $rotate . 'uuuxuuu';
    $x = substr($part,6,3);// - 500;
    $y = substr($part,10,3);// - 500;
    $x -= 500;
    $y -= 500;
    $minX = min($x,$minX);
    $minY = min($y,$minY);
    if ($fill=='u') {
      $temp = base2view($base);
      $fill = substr($temp,3,1);
    }
    if ($rotate=='u') {
      $rotate=0;
    }
    $raw .= 'S' . $base . $fill . $rotate . koord2str($x,$y);
  }
  $ksw='';
  if ($raw!="M"){
    global $sym_sizer;
    if(!$sym_sizer) sym_init();
    $sym_sizer->load($raw);
    $expanded = $sym_sizer->expand($raw);

    $ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
    $ksw_coord = 'n?[0-9]+xn?[0-9]+';
    $ksw_syms = $ksw_sym . $ksw_coord . 'x' . $ksw_coord;
    $ksw_pattern = '/' . $ksw_syms . '/i';
    preg_match_all($ksw_pattern,$expanded, $matches);
    $Xmax=array();
    $Ymax=array();
    foreach($matches[0] as $spatial){
      $len = strlen($spatial);
      $nums = explode('x', substr($spatial,6,$len-6));
      $size = str2koord($nums[0] . 'x' . $nums[1]);
      $place = str2koord($nums[2] . 'x' . $nums[3]);
      $Xmax[] = $place[0] + $size[0];
      $Ymax[] = $place[1] + $size[1];
    }
    $ksw = str_replace("M","M" . koord2str(max($Xmax),max($Ymax)),$raw);
  }
  
  return $ksw;
}

/** 
 * @brief convert fsw query to symbol list
 * @param $qsearch fsw query
 * @return string of symbol keys
 * @ingroup conv
 */
function query2syms($qsearch){
  if (!fswQuery($qsearch)) return;
  $fsw_sym = 'S[123][0-9a-f]{2}[0-5u][0-9a-fu]';
  $fsw_pattern = '/' . $fsw_sym . '/i';

  $result = preg_match_all($fsw_pattern,$qsearch,$matches);
  $iswa = '';
  foreach ($matches[0] as $part){
    $iswa .= $part;
  }
  
  return $iswa;
}

/** 
 * @brief convert fsw query to range list
 * @param $qsearch fsw query
 * @return string of ranges
 * @ingroup conv
 */
function query2ranges($qsearch){
  if (!fswQuery($qsearch)) return;
  $fsw_pattern = 'R[123][0-9a-f]{2}t[123][0-9a-f]{2}';
  $fsw_pattern = '/' . $fsw_pattern . '/i';

  $result = preg_match_all($fsw_pattern,$qsearch,$matches);
  $ranges = '';
  foreach ($matches[0] as $part){
    $ranges .= $part;
  }
  return $ranges;
}

/** 
 * @brief remove coordinates from query string
 * @param $qsearch fsw query
 * @return string without coordinates
 * @ingroup conv
 */
function query2anywhere($qsearch){
  if (!fswQuery($qsearch)) return;
  $fsw_coord = '/[0-9]{3}x[0-9]{3}/';
  $result = preg_match_all($fsw_coord,$qsearch,$matches);
  foreach ($matches[0] as $part){
    $qsearch = str_replace($part,'',$qsearch);
  }
  return $qsearch;
}
?>
