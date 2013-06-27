<?php
/**
 * Formal SignWrting Library
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
 * @brief Formal SignWriting for regular expression searching
 * @file
 *   
 */

/** @defgroup fsw Formal SignWriting
 *  regular text for searching
 */

/** @defgroup fre Regular Expressions 
 *  @ingroup fsw
 *  Regular expressions for regular text
 */
 
/** 
 * @brief test if text is Formal SignWriting 
 * @param $text character string
 * @return boolean value if text is Formal SignWriting with regular numbers
 * @ingroup fre
 */
function fswText($text){
  $fsw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  $fsw_coord = '[0-9]{3}x[0-9]{3}';
  $fsw_word = '(A(' . $fsw_sym. ')+)?[BLMR](' . $fsw_coord . ')(' . $fsw_sym . $fsw_coord . ')*';
  $fsw_punc = 'S38[7-9ab][0-5][0-9a-f]' . $fsw_coord;
  $fsw_pattern = '/^(' . $fsw_word . '|' . $fsw_punc . ')( ' . $fsw_word . '| ' . $fsw_punc .')*$/i';

  $result = preg_match($fsw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return true;
    }
  }
  return false;
}

/** 
 * @brief test if text is Formal SignWriting Query
 * @param $text character string
 * @return boolean value if text is Formal SignWriting Query
 * @ingroup fre
 */
function fswQuery($text){
  $fsw_range = 'R[123][0-9a-f]{2}t[123][0-9a-f]{2}';
  $fsw_sym = 'S[123][0-9a-f]{2}[0-5u][0-9a-fu]';
  $fsw_coord = '([0-9]{3}x[0-9]{3})?';
  $fsw_var = '(V[0-9]+)?';
  $fsw_query = 'QT?(' . $fsw_range . $fsw_coord . ')*(' . $fsw_sym . $fsw_coord . ')*' . $fsw_var;
  $fsw_pattern = '/^' . $fsw_query . '$/i';

  $result = preg_match($fsw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return true;
    }
  }
  return false;
}

/** 
 * @brief convert fsw to query
 * @param fsw formal signwriting text
 * @return query string for searching
 * @ingroup conv
 */
function fsw2query($fsw){
  if (!fswText($fsw)) return;
  $fsw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  $fsw_coord = '[0-9]{3}x[0-9]{3}';
  $fsw_query = $fsw_sym . $fsw_coord;
  $fsw_pattern = '/' . $fsw_query . '/i';

  $result = preg_match_all($fsw_pattern,$fsw,$matches);
  $query = 'Q';
  foreach ($matches[0] as $part){
    $query .= $part;
  }
  
  return $query;
}


/** 
 * @brief convert range to regular expression pattern
 * @param $min minimum value
 * @param $max max value
 * @param $hex flag for hexadecimal range
 * @param $test flag for test output
 * @return regular expression for range testing
 * @ingroup fre
 */
function range2regex($min,$max,$hex='',$test=''){
  $min = str_pad($min,3,'0',STR_PAD_LEFT);  
  $max = '' . $max;
  $pattern='';
//  if ($val=='uuu') return '[0-9]{3}';
  //assume numbers are 3 digits long

  if ($min===$max) return $min;

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
          break;
        case "hh":
          $pattern .= '[' . $min[1];
          if ($diff>1) $pattern .= '-';
          $pattern .= dechex(hexdec($max[1])-1) . ']';
          break;
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

/** 
 * @brief convert formal SignWriting query to regular expression pattern
 * @param $query formal query string
 * @return array of regular expressions for searching
 * @ingroup fre
 */
function query2regex ($query,$fuzz=''){
  if ($fuzz=='') $fuzz = 20;

  $re_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  $re_coord = '[0-9]{3}x[0-9]{3}';
  $re_word = '[BLMR](' . $re_coord . ')(' . $re_sym . $re_coord . ')*';
  $re_term = '(A(' . $re_sym. ')+)';

  $fsw_range = 'R[123][0-9a-f]{2}t[123][0-9a-f]{2}';
  $fsw_sym = 'S[123][0-9a-f]{2}[0-5u][0-9a-fu]';
  $fsw_coord = '([0-9]{3}x[0-9]{3})?';
  $fsw_var = '(V[0-9]+)';
  $fsw_query = 'QT(' . $fsw_range . $fsw_coord . ')*(' . $fsw_sym . $fsw_coord . ')*' . $fsw_var . '?';

  if (!fswQuery($query)) return;

  if (!$query || $query=='Q'){
    return array('/' . $re_word . '/');
  }

  if (!$query || $query=='QT'){
    return array('/' . $re_term . $re_word . '/');
  }

  $segments = array();

  $term = strpos($query,'T');

  //get the variance
  $fsw_pattern = '/' . $fsw_var . '/i';
  $result = preg_match($fsw_pattern,$query,$matches);
  if ($result) $fuzz = substr($matches[0],1);

  //this gets all symbols with or without location
  $fsw_pattern = '/' . $fsw_sym . $fsw_coord . '/i';
  $result = preg_match_all($fsw_pattern,$query,$matches);
  if ($result) {
    foreach ($matches[0] as $part){
      $base = substr($part,1,3);
      $segment = 'S' . $base;

      $fill = substr($part,4,1);
      if ($fill=='u') {
        $segment .= '[0-5]';
      } else {
        $segment .= $fill;
      }
    
      $rotate = substr($part,5,1);
      if ($rotate=='u') {
        $segment .= '[0-9a-f]';
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
      if ($term) $segment = $re_term . $segment;
      $segment= '/' . $segment . '/';
      $segments[]= $segment;
    }
  }

  //this gets all ranges
  $fsw_pattern = '/' . $fsw_range . $fsw_coord . '/';
  $result = preg_match_all($fsw_pattern,$query,$matches);
  if ($result) {
    foreach ($matches[0] as $part){
      $from = substr($part,1,3);
      $to = substr($part,5,3);
      $re_range = range2regex($from,$to,"hex");
      $segment = 'S' . $re_range . '[0-5][0-9a-f]';
      if (strlen($part)>8){

        $x = substr($part,8,3);
        $y = substr($part,12,3);
        //now get the x segment range...
        $segment .= range2regex(($x-$fuzz),($x+$fuzz));
        $segment .= 'x';
        $segment .= range2regex(($y-$fuzz),($y+$fuzz));
      } else {
        $segment .= $re_coord;
      }
      // add to general ksw word
      $segment = $re_word . $segment . '(' . $re_sym . $re_coord . ')*';
      if ($term) $segment = $re_term . $segment;
      $segment= '/' . $segment . '/';
      $segments[]= $segment;
    }
  }
  
  return $segments;
}

/** 
 * @brief return displacement query search string
 * @param $qsearch formal query search string
 * @param $x displacement value for X
 * @param $y displacement value for Y
 * @return displaced query search string
 * @ingroup fre
 */
function query_displace($qsearch,$x,$y){
  $fsw_coord = '[0-9]{3}x[0-9]{3}';

  //this gets all symbols with or without location
  $fsw_pattern = '/' . $fsw_coord . '/i';
  $result = preg_match_all($fsw_pattern,$qsearch,$matches);
  foreach ($matches[0] as $str){
    $coord = str2coord($str);
    $coord[0] += $x;
    $coord[1] += $y;
    $new = coord2str($coord[0],$coord[1]);
    $qsearch = str_replace($str,$new,$qsearch);
  }
  return $qsearch;
}

/** 
 * @brief return displacement query search strings
 * @param $qsearch formal query search string
 * @return array of displacement query search strings
 * @ingroup fre
 */
function query2displace($qsearch){
  $fuzz=20;
  if (!fswQuery($qsearch)) return;

  $fsw_var = '(V[0-9]+)';

  //get the variance
  $fsw_pattern = '/' . $fsw_var . '/i';
  $result = preg_match($fsw_pattern,$qsearch,$matches);
  if ($result) $fuzz = intval(substr($matches[0],1));

  $qsa = array();
  $qsa[] = query_displace($qsearch,$fuzz*-2,$fuzz*-2);
  $qsa[] = query_displace($qsearch,0,$fuzz*-2);
  $qsa[] = query_displace($qsearch,$fuzz*2,$fuzz*-2);
  $qsa[] = query_displace($qsearch,$fuzz*-2,0);
  $qsa[] = query_displace($qsearch,$fuzz*2,0);
  $qsa[] = query_displace($qsearch,$fuzz*-2,$fuzz*2);
  $qsa[] = query_displace($qsearch,0,$fuzz*2);
  $qsa[] = query_displace($qsearch,$fuzz*2,$fuzz*2);
  
  return $qsa;
}

/** 
 * @brief execute query against input string
 * @param $qsearch formal query search string
 * @param $input input string to be searched
 * @return array of matching words
 * @ingroup fre
 */
function query_results($qsearch,$input){
  //return array of words
  
  $re = query2regex($qsearch);
  foreach ($re as $pattern){
    $count = preg_match_all($pattern, $input, $matches);
    $input = implode(array_unique($matches[0]),' ');
    //normalize to M or B ?
    $input = str_replace('L','M',$input);
    $input = str_replace('R','M',$input);
    $input = str_replace('B','M',$input);
  }

  if ($input){
    $words = array_unique(explode(' ',$input));
  } else {
    $words = array();
  }

  return $words;
}

/** 
 * @brief execute query against input string and include counts
 * @param $qsearch formal query search string
 * @param $input input string to be searched
 * @return array as array words, array of word counts, and grand total
 * @ingroup fre
 */
function query_counts($qsearch,$input){
  //return array[0] array of words
  //return array[1] array of word counts
  //return array[2] value of grand total
  
  $cnt='';
  $re = query2regex($qsearch);
  foreach ($re as $pattern){
    $count = preg_match_all($pattern, $input, $matches);
    // this gets word counts for the first match only match!
    // following searches are subset
    if (!is_array($cnt)){
      $cnt = array();
      foreach ($matches[0] as $match){
        $match[0]='M';
        if (array_key_exists($match,$cnt)){
          $cnt[$match]++;
        } else {
          $cnt[$match]=1;;
        }
      }
    }
    $input = implode(array_unique($matches[0]),' ');
    //normalize to M or B ?
    $input = str_replace('L','M',$input);
    $input = str_replace('R','M',$input);
    $input = str_replace('B','M',$input);
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

?>
