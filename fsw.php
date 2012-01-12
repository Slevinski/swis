<?php
/**
 * Cartesian SignWrting library
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
 * @version 1.3.0
 * @filesource
 *   
 */

/**
 * Cartesian SignWriting rich text script encoding
 *   ksw = lite markup with 5 hex digit string for symbols
 *   csw = lite markup with proposed Unicode string of 3 plane 1 characters for symbols
 *   fsw = lite markup with 5 hex digit string for symbol and a canvas center of 500
 *    
 */
 
function fswText($text){
  $fsw_sym = 'S[123][a-f0-9]{2}[012345][a-f0-9]';
  $fsw_coord = '[0-9]{3}x[0-9]{3}';
  $fsw_word = '(A(' . $fsw_sym. ')+)?[LMR](' . $fsw_coord . ')(' . $fsw_sym . $fsw_coord . ')*';
  $fsw_punc = 'S38[a-f0-9][012345][a-f0-9]' . $fsw_coord;
  $fsw_pattern = '/^(' . $fsw_word . '|' . $fsw_punc . ')( ' . $fsw_word . '| ' . $fsw_punc .')*$/i';

  $result = preg_match($fsw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return 1;
    }
  }
  return 0;
}

/**
 * fuzzy query string 
 */
function fswQuery($text){
  $fsw_range = 'R[123][a-f0-9]{2}t[123][a-f0-9]{2}';
  $fsw_sym = 'S[123][a-f0-9]{2}[012345u][a-f0-9u]';
  $fsw_coord = '([0-9u]{3}x[0-9u]{3})?';
  $fsw_var = '(V[0-9]+)?';
  $fsw_query = 'Q(' . $fsw_range . ')*(' . $fsw_sym . $fsw_coord . ')*' . $fsw_var;
  $fsw_pattern = '/^' . $fsw_query . '$/i';

  $result = preg_match($fsw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return 1;
    }
  }
  return 0;
}

function getplace($val,$i){
  $len = strlen($val);
  if ($i>=$len){
    return 0;
  } else {
    return substr($val,$i-1,1);
  }
}

function range2regex($min,$max,$hex,$test){
  if ($max>999) $max='999';
  if ($min <0) $min = '000';
  $min = str_pad($min,3,'0',STR_PAD_LEFT);  
  $max = '' . $max;
  if ($val=='uuu') return '[0-9]{3}';
  //assume numbers are 3 digits long

  if ($min==$max) return $min;
  if ($min>$max) return '';

if ($test) echo "<h3>Original values $min</h3>";

  //ending pattern will be series of connected OR ranges
  $re = array();

  //first pattern.  10's don't match and the min 1's are not zero
  //odd number to 9
  if (!($min[0]==$max[0] && $min[1]==$max[1])) {
    if ($min[2]!='0'){
      $pattern = $min[0] . $min[1];
      if ($hex) {
        //switch for dex
        switch ($min[2]){
        case "f":
          $pattern .= 'f';
          break;
        case "e":
          $pattern .= '[ef]';
          break;
        case "d";
        case "c";
        case "b";
        case "a";
          $pattern .= '[' . $min[2] . '-f]';
          break;
        default:
          switch ($min[2]){
            case "9":
           $pattern .= '[9a-f]';
            break;
          case "8":
            $pattern .= '[89a-f]';
            break;
          default:
           $pattern .= '[' . $min[2] . '-9a-f]';
            break;
          }
          break;
        }
        $diff = 15-hexdec($min[2]) +1;
        $min = '' . dechex((hexdec($min)+$diff));
        $re[] =$pattern; 
      } else {
        //switch for dex
        switch ($min[2]){
        case "9":
          $pattern .= '9';
          break;
        case "8":
          $pattern .= '[89]';
          break;
        default:
         $pattern .= '[' . $min[2] . '-9]';
          break;
        }
        $diff = 9-$min[2] +1;
        $min = '' . ($min+$diff);
        $re[] =$pattern; 
      }
    }
  }
if ($test) {
  echo "<h3>Bring up the non zero digits</li></h3>";
  if ($pattern) {
    echo "<p>Step One: $pattern for new values $min";
  } else {
    echo "<p>Step One: NA";
  }
}
$pattern = '';

  //if hundreds are different, get odd to 99 or ff
  if ($min[0]!=$max[0]){
    if ($min[1]!='0'){
      if ($hex){
        //scrape to ff
        $pattern = $min[0];
        switch ($min[1]){
        case "f":
          $pattern .= 'f';
          break;
        case "e":
          $pattern .= '[ef]';
          break;
        case "d":
        case "c":
        case "b":
        case "a":
         $pattern .= '[' . $min[1] . '-f]';
          break;
        case "9":
         $pattern .= '[9a-f]';
          break;
        case "8":
         $pattern .= '[89a-f]';
          break;
        default:
         $pattern .= '[' . $min[1] . '-9a-f]';
          break;
        }
        $pattern .= '[0-9a-f]';
        $diff = 15-hexdec($min[1]) +1;
        $min = '' . dechex(hexdec($min)+$diff*16);
        $re[] =$pattern; 
      } else {
        //scrape to 99
        $pattern = $min[0];
        $diff = 9-$min[1] +1;
        switch ($min[1]){
        case "9":
          $pattern .= '9';
          break;
        case "8":
          $pattern .= '[89]';
          break;
        default:
         $pattern .= '[' . $min[1] . '-9]';
          break;
        }
        $pattern .= '[0-9]';
        $diff = 9-$min[1] +1;
        $min = '' . ($min+$diff*10);
        $re[] =$pattern; 
      }
    }
  }
if ($test) {
  echo "<h3>Bring up the 10's if hundreds are different</h3>";
  if ($pattern) {
    echo "<p>Step Two: $pattern for new values $min";
  } else {
    echo "<p>Step Two: NA";
  }
}
$pattern = '';

  //if hundreds are different, get to same
  if ($min[0]!=$max[0]){
    if ($hex){
      $diff = hexdec($max[0]) - hexdec($min[0]);
      $tmax = dechex(hexdec($min[0]) + $diff-1);
    
      switch ($diff){
      case 1:
        $pattern = $min[0];
        break;
      case 2:
        $pattern = '[' . $min[0] . $tmax . ']';
        break;
      default:
        if (hexdec($min[0])>9){
          $minV = 'h';
        } else {
          $minV = 'd';
        }
        if (hexdec($tmax)>9){
          $maxV = 'h';
        } else {
          $maxV = 'd';
        }
        switch ($minV . $maxV){
        case "dd":
          $pattern .= '[' . $min[0] . '-' . $tmax . ']';
          break;
        case "dh":
          $diff = 9 - $min[0];
          //firs get up to 9
          switch ($diff){
          case 0:
            $pattern .= '[9';
            break;
          case 1:
            $pattern .= '[89';
            break;
          default:
            $pattern .= '[' . $min[0] . '-9';
            break;
          }
          switch ($tmax[0]){
          case 'a':
            $pattern .= 'a]';
            break;
          case 'b':
            $pattern .= 'ab]';
            break;
          default:
            $pattern .= 'a-' . $tmax . ']';
            break;
          }
          break;
        case "hh":
          $pattern .= '[' . $min[0] . '-' . $tmax . ']';
          break;
        }
      }

      $pattern .= '[0-9a-f][0-9a-f]';
      $diff = hexdec($max[0]) - hexdec($min[0]);
      $min = '' . dechex(hexdec($min)+$diff*256);
      $re[] =$pattern; 
    } else {
      $diff = $max[0] - $min[0];
      $tmax = $min[0] + $diff-1;
    
      switch ($diff){
      case 1:
        $pattern = $min[0];
        break;
      case 2:
        $pattern = '[' . $min[0] . $tmax . ']';
        break;
      default:
       $pattern = '[' . $min[0] . '-' . $tmax . ']';
        break;
      }
      $pattern .= '[0-9][0-9]';
      $min = '' . ($min+$diff*100);
      $re[] =$pattern; 
    }
  }
if ($test) {
  echo "<h3>Bring up the 100's if different</h3>";
  if ($pattern) {
    echo "<p>Step Three: $pattern for new values $min";
  } else {
    echo "<p>Step Three: NA";
  }
}
$pattern = '';

  //if tens are different, get to same
  if ($min[1]!=$max[1]){
    if ($hex){
      $diff = hexdec($max[1]) - hexdec($min[1]);
      $tmax = dechex(hexdec($min[1]) + $diff-1);
      $pattern = $min[0];
      switch ($diff){
      case 1:
        $pattern .= $min[1];
        break;
      case 2:
        $pattern .= '[' . $min[1] . $tmax . ']';
        break;
      default:

        if (hexdec($min[1])>9){
          $minV = 'h';
        } else {
          $minV = 'd';
        }
        if (hexdec($tmax)>9){
          $maxV = 'h';
        } else {
          $maxV = 'd';
        }
        switch ($minV . $maxV){
        case "dd":
          $pattern .= '[' . $min[1];
          if ($diff>1) $pattern .= '-';
          $pattern .= $tmax . ']';
          break;
        case "dh":
          $diff = 9 - $min[1];
          //firs get up to 9
          switch ($diff){
          case 0:
            $pattern .= '[9';
            break;
          case 1:
            $pattern .= '[89';
            break;
          default:
            $pattern .= '[' . $min[1] . '-9';
            break;
          }
          switch ($max[1]){
          case 'a':
            $pattern .= ']';
            break;
          case 'b':
            $pattern .= 'a]';
            break;
          default:
            $pattern .= 'a-' . dechex(hexdec($max[1])-1) . ']';
            break;
          }
        }
        break;
      }
      $pattern .= '[0-9a-f]';
      $diff = hexdec($max[1]) - hexdec($min[1]);
      $min = '' . dechex(hexdec($min)+$diff*16);
      $re[] =$pattern; 
    } else {
      $diff = $max[1] - $min[1];
      $tmax = $min[1] + $diff-1;
      $pattern = $min[0];
      switch ($diff){
      case 1:
        $pattern .= $min[1];
        break;
      case 2:
        $pattern .= '[' . $min[1] . $tmax . ']';
        break;
      default:
       $pattern .= '[' . $min[1] . '-' . $tmax . ']';
        break;
      }
      $pattern .= '[0-9]';
      $min = '' . ($min+$diff*10);
      $re[] =$pattern; 
    }

  }
if ($test) {
  echo "<h3>Bring up the 10's</h3>";
  if ($pattern) {
    echo "<p>Step Four: $pattern for new values $min";
  } else {
    echo "<p>Step Four: NA";
  }
}
$pattern = '';

  //if digits are different, get to same
  if ($min[2]!=$max[2]){
    if ($hex){
      $pattern = $min[0] . $min[1];
      $diff = hexdec($max[2]) - hexdec($min[2]);
      if (hexdec($min[2])>9){
        $minV = 'h';
      } else {
        $minV = 'd';
      }
      if (hexdec($max[2])>9){
        $maxV = 'h';
      } else {
        $maxV = 'd';
      }
      switch ($minV . $maxV){
      case "dd":
        $pattern .= '[' . $min[2];
        if ($diff>1) $pattern .= '-';
        $pattern .= $max[2] . ']';
        break;
      case "dh":
        $diff = 9 - $min[2];
        //firs get up to 9
        switch ($diff){
        case 0:
          $pattern .= '[9';
          break;
        case 1:
          $pattern .= '[89';
          break;
        default:
          $pattern .= '[' . $min[2] . '-9';
          break;
        }
        switch ($max[2]){
        case 'a':
          $pattern .= 'a]';
          break;
        case 'b':
          $pattern .= 'ab]';
          break;
        default:
          $pattern .= 'a-' . $max[2] . ']';
          break;
        }
        
        break;
      case "hh":
        $pattern .= '[' . $min[2];
        if ($diff>1) $pattern .= '-';
        $pattern .= $max[2] . ']';
        break;
      }
      $diff = hexdec($max[2]) - hexdec($min[2]);
      $min = '' . dechex(hexdec($min) + $diff);
      $re[] =$pattern; 
    } else {
      $diff = $max[2] - $min[2];
      $pattern = $min[0] . $min[1];
      switch ($diff){
      case 0:
        $pattern .= $min[2];
        break;
      case 1:
        $pattern .= '[' . $min[2] . $max[2] . ']';
        break;
      default:
       $pattern .= '[' . $min[2] . '-' . $max[2] . ']';
        break;
      }
      $min = '' . ($min+$diff);
      $re[] =$pattern; 
    }
  }
if ($test) {
  echo "<h3>Bring up the 1's</h3>";
  if ($pattern) {
    echo "<p>Step Five: $pattern for new values $min";
  } else {
    echo "<p>Step Five: NA";
  }
}
$pattern = '';



  //last place is whole hundred
  if ($min[2]=='0' && $max[2]=='0') {
    $pattern = $max;
    $re[] =$pattern;
  }
if ($test) {
  echo "<h3>Match Zero endings</h3>";
  if ($pattern) {
    echo "<p>Step Six: $pattern for new values $min";
  } else {
    echo "<p>Step Six: NA";
  }
}
$pattern = '';
  
  $cnt = count($re);
  if ($cnt==1){
    $pattern = $re[0];
  } else {
    $pattern = implode($re,')|(');
    $pattern = '((' . $pattern . '))';
  }
  return $pattern;
}

function fquery2regex ($query,$fuzz=''){
  if ($fuzz=='') $fuzz = 20;

  $re_sym = 'S[123][a-f0-9]{2}[012345][a-f0-9]';
  $re_coord = '[0-9]{3}x[0-9]{3}';
  $re_word = '[LMR](' . $re_coord . ')(' . $re_sym . $re_coord . ')*';
  if (!$query || $query=='Q'){
    return array('/' . $re_word . '/');
  }

  $fsw_range = 'R[123][a-f0-9]{2}t[123][a-f0-9]{2}';
  $fsw_sym = 'S[123][a-f0-9]{2}[012345u][a-f0-9u]';
  $fsw_coord = '([0-9u]{3}x[0-9u]{3})?';
  $fsw_var = '(V[0-9]+)';
  $fsw_query = 'Q(' . $fsw_range . ')*(' . $fsw_sym . $fsw_coord . ')*' . $fsw_var . '?';

  if (!fswQuery($query)) return;

  //get the variance
  $fsw_pattern = '/' . $fsw_var . '/i';
  $result = preg_match($fsw_pattern,$query,$matches);
  if ($result) $fuzz = substr($matches[0],1);

  //this gets all symbols with or without location
  $fsw_pattern = '/' . $fsw_sym . $fsw_coord . '/i';
  $result = preg_match_all($fsw_pattern,$query,$matches);
  $segments = array();
  if ($result) {
    foreach ($matches[0] as $part){
      $base = substr($part,1,3);
      $segment = 'S' . $base;

      $fill = substr($part,4,1);
      if ($fill=='u') {
        $segment .= '[012345]';
      } else {
        $segment .= $fill;
      }
    
      $rotate = substr($part,5,1);
      if ($rotate=='u') {
        $segment .= '[a-f0-9]';
      } else {
        $segment .= $rotate;
      }
      if (strlen($part)>6){

        $x = substr($part,6,3);
        $y = substr($part,10,3);
        //now get the x segment range...
        $segment .= range2regex(($x-$fuzz),($x+$fuzz));
        $segment .= 'x';
        $segment .= range2regex(($y-$fuzz),($y+$fuzz));
      } else {
        $segment .= $re_coord;
      }
      //now I have the specific search symbol
      // add to general ksw word
      $segment = $re_word . $segment . '(' . $re_sym . $re_coord . ')*';

      $segment= '/' . $segment . '/';
      $segment .= 'i';
      $segments[]= $segment;
    }
  }

  //this gets all ranges
  $fsw_pattern = '/' . $fsw_range . '/i';
  $result = preg_match_all($fsw_pattern,$query,$matches);
  if ($result) {
    foreach ($matches[0] as $part){
      $from = substr($part,1,3);
      $to = substr($part,5,3);
      $re_range = range2regex($from,$to,"hex");
      $segment = 'S' . $re_range . '[0-5][a-f0-9]' . $re_coord;
      // add to general ksw word
      $segment = $re_word . $segment . '(' . $re_sym . $re_coord . ')*';

      $segment= '/' . $segment . '/';
      $segment .= 'i';
      $segments[]= $segment;
    }
  }
  
  return $segments;
}

function query_counts($qsearch,$input){
  //return array[0] output string of words with spaces
  //return array[1] array of word counts
  //return array[2] value of grand total
  
  $re = fquery2regex($qsearch);

  foreach ($re as $pattern){
    $count = preg_match_all($pattern, $input, $matches);
    // this gets word counts for the first match only match!
    // following searches are subset
    if (!is_array($cnt)){
      $cnt = array();
      foreach ($matches[0] as $match){
        $match[0]='M';
        $cnt[$match]++;
      }
    }
    $input = implode(array_unique($matches[0]),' ');
    $input = str_replace('L','M',$input);
    $input = str_replace('R','M',$input);
  }

  if ($input){
    $words = array_unique(explode(' ',$input));
  } else {
    $words = array();
  }
  //display signs
  $wcount = count($words);

  if (!trim($input)) $wcount=0;

  $gtot = 0;
  foreach ($words as $word){
    $gtot += $cnt[$word];
  }

  $return = array();
  $return[]=$words;
  $return[]= $cnt;
  $return[]=$gtot;
  return $return;
  
}

function query2table($qsearch){
  $grid = array();
  $col = array();
  $col[0] = 'Approximate location';
  $ksw = query2ksw($qsearch);
  if ($ksw){
    $cluster = ksw2cluster($ksw);
    $real = cluster2min($cluster);
    
    $adj=array($real[0],$real[1]);
    $ksw = raw2ksw($ksw);
    $ksw = crosshairs($ksw,$adj);
    $val = '<img src="glyphogram.php?ksw=' . $ksw . '">';
    $symsearch = query2syms($qsearch);
    //this should point to this page not searchquery always
    $val .= '<hr><p><a href="searchquery.php?qsearch=' . $symsearch .'">Ignore location?</a>';
    $col[1]=$val;
    $grid[]=$col;
  }
  
  $col=array();
  $col[0] = 'Symbol list';
  $iswa = query2iswa($qsearch);
  $syms=array();
  if ($iswa) $syms = str_split($iswa,6);
  $iswa = '';
  foreach ($syms as $part){
    $base = substr($part,1,3);
    $fill = substr($part,4,1);
    $rotate = substr($part,5,1);
    if ($fill=='u'){
      if ($rotate=='u'){
        $match = getSignTitle(34,"ui");//any
      } else {
        $match = getSignTitle(36,"ui");//rotate
      }
    } else {
      if ($rotate=='u'){
        $match = getSignTitle(35,"ui");//fill
      } else {
        $match = getSignTitle(33,"ui");//exact
      }
    }

    if ($fill=='u') {
      $temp = base2view($base);
      $fill = substr($temp,3,1);
    }
    if ($rotate=='u') {
      $rotate=0;
    }
    $iswa .= '<p><img src="glyph.php?key=' . $base . $fill . $rotate . '"> - ' . $match;
  }
  if($iswa){
    $col[1] = $iswa;
    $grid[]=$col;
  }

  //now check for ranges
  $col=array();
  $col[0] = 'Base range';
  $base_range = '';
  $ranges = query2ranges($qsearch);
  $base_range = '';
  if ($ranges) $ranges = str_split($ranges,8);
  foreach ($ranges as $range){
    $base_range .= '<p>';
    $from = substr($range,1,3);
    $to = substr($range,5,3);
    $base_range.= '<img src="glyph.php?key=' . base2view($from) . '"> - ';
    $base_range.= '<img src="glyph.php?key=' . base2view($to) . '">';
  }
  if ($base_range){
    $col[1] = $base_range;
    $grid[]=$col;
  }
  

  $return = "<table cellpadding=15 border=1><tr>";
  foreach ($grid as $col){
    $return.= '<th>' . $col[0] . '</th>';
  }
  $return .= '</tr><tr>';
  foreach ($grid as $col){
    $return .= '<td valign=top>';
    $return .=  $col[1];
    $return .= '</td>';
  }
  $return .= '</tr></table>';
  if ($return == '<table><tr></tr></table>') echo "All signs";
  $return .= '<br><hr><a href="frequency.php?ui=' . $ui . '&sgn=' . $sgn . '&qsearch=' . $qsearch . '">';
  $return .= 'Symbol Frequency</a>';
  $return .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="searchquery.php?ui=' . $ui . '&sgn=' . $sgn . '&qsearch=' . $qsearch . '">';
  $return .= 'Search Results</a><hr><br>';

  return $return;
}
/**
 * this can be removed
 * kind of works for ksw or csw
 
 function allthis($max){
  $len = strlen($max);
  $pattern='';
  for($i=0;$i<$len;$i++){
    $pattern .= '[0-9]';
  }
  return $pattern;
}

function query2regex ($query,$utf){
  if (!kswQuery($query)) return;

  if ($utf) {
    $re_sym ='[\x{1D800}-\x{1DA8B}][\x{1DA9A}-\x{1DA9F}][\x{1DAA0}-\x{1DAAF}]';
  } else {
    $re_sym = 'S[123][a-f0-9]{2}[012345][a-f0-9]';
  }
  $re_coord = 'n?[0-9]+xn?[0-9]+';
  $re_word = '(A(' . $re_sym. ')+)?[LMR](' . $re_coord . ')(' . $re_sym . $re_coord . ')*';

  $query = str_replace('Q','',$query);
  $parts = str_split($query,8);
  $segments = array();
  foreach ($parts as $part){
    $base = substr($part,1,3);
    if ($utf){
      $segment ='\x{' . char2unicode($base) . '}';
    } else {
      $segment = 'S' . $base;
    }

    $fill = substr($part,4,1);
    if ($utf){
      if ($fill=='u') {
        $segment .='[\x{1DA9A}-\x{1DA9F}]';
      } else {
        $segment .='\x{' . char2unicode(fill2char($fill)) . '}';
      }
    } else {
      if ($fill=='u') {
        $segment .= '[012345]';
      } else {
        $segment .= $fill;
      }
    }
    
    $rotate = substr($part,5,1);
    if ($utf){
      if ($rotate=='u') {
        $segment .='[\x{1DAA0}-\x{1DAAF}]';
      } else {
        $segment .='\x{' . char2unicode(rot2char($rotate)) . '}';
      }
    } else {
      if ($rotate=='u') {
        $segment .= '[a-f0-9]';
      } else {
        $segment .= $rotate;
      }
    }

    $x = substr($part,6,1);
    if ($x=='n') {
      $segment .= 'n[0-9]+x';
    } else if ($x=='u') {
      $segment .= 'n?[0-9]+x';
    } else if ($x=='p'){
      $segment .= '(n[0-9]x)|([0-9]+x)';  //adjust for center placement
    }
    
    $y = substr($part,7,1);
    if ($y=='n') {
      $segment .= 'n[0-9]+';
    } else if ($y=='u') {
      $segment .= 'n?[0-9]+';
    } else if ($y=='p') {
//      $segment .= '(n[0-9](?![0-9]))|([0-9]+)';  //adjust for center placement
      $segment .= '[0-9]+';
    }

    //now I have the specific search symbol
    // add to general ksw word
    $segment = $re_word . $segment . '(' . $re_sym . $re_coord . ')*';

    $segment= '/' . $segment . '/';
    if ($utf) {
      $segment .= 'u';
    } else {
      $segment .= 'i';
    }
    $segments[]= $segment;
  }
  
  return $segments;
}
?>
