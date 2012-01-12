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
 
include('bsw.php'); // open directly
include ('ksw.php'); //display library
include ('fsw.php'); //searching

$min = $_REQUEST['min'];
$max = $_REQUEST['max'];
$hex = $_REQUEST['hex'];
$test = $_REQUEST['test'];
if ($test) range_check($min,$max,$hex,$test);
function range_check($min,$max,$hex,$test){
  echo "<p>Testing range $min to $max with pattern " . range2regex($min,$max,$hex,1) . '<hr>';
}

//Text Strings in KSW
$hello = "S10001";
$hello_seq = "S10001";
$world = "S10f10n4xn1";
$world_seq = "S10f10";
$period = "";
$hello_world = $hello . $world . $period;
//echo csw2fsw("A𝣅𝪞𝪬𝣅𝪛𝪤𝦈𝪚𝪠𝦈𝪛𝪨𝤅𝪚𝪠𝤅𝪚𝪠𝤅𝪚𝪠𝤅𝪚𝪠𝩭𝪚𝪠𝩭𝪚𝪠M47x84𝩭𝪚𝪠n21xn18𝩭𝪚𝪠n22x14𝤅𝪚𝪠17x11𝤅𝪚𝪠n28x11𝤅𝪚𝪠n47x45𝤅𝪚𝪠37x45𝦈𝪚𝪠26x23𝦈𝪛𝪨n39x25𝣅𝪛𝪤n42x56𝣅𝪞𝪬11x55");
//ksw words
//AS10000M15x25S100000x0
//M15x25S100000x0
//D150x300_M25x10S100000x0_M25x50S100000x0_M25x100S100000x0_M25x150S100000x0_S3880025x200
//S100000x0
//S38800
//S38800n14xn22


/* functions to be removed...
function validWord($ksw){
  $ksw_sym = 'S[123][a-f0-9]{2}[012345][a-f0-9]';
  $ksw_coord = 'n?[0-9]+xn?[0-9]+';
  $ksw_word = '(A(' . $ksw_sym. ')+)?[LMR](' . $ksw_coord . ')(' . $ksw_sym . $ksw_coord . ')*';
  $ksw_pattern = '/^' . $ksw_word . '$/i';
  $result = preg_match($ksw_pattern,$ksw,$matches);
  if ($result) {
    if ($ksw == $matches[0]) {
      return 1;
    }
  }
  return 0;
}
function validKSW($ksw){
  $ksw_pattern = '/^(A(S[123][a-f0-9]{2}[012345][a-f0-9])+)?[LMR](n?[0-9]+xn?[0-9]+)?(S[123][a-f0-9]{2}[012345][a-f0-9]n?[0-9]+xn?[0-9]+)*$/i';
  $result = preg_match($ksw_pattern,$ksw,$matches);
  if ($result) {
    if ($ksw == $matches[0]) {
      return 1;
    }
  }
  return 0;
}
*/


/* to be removed
function kswQuery($text){
  $ksw_sym = 'S[123][a-f0-9]{2}[012345u][a-f0-9u]';
  $ksw_usym ='/[\x{1D800}-\x{1DA8B}][\x{1DA9A}-\x{1DA9F}u][\x{1DAA0}-\x{1DAAF}u]/u';
  $ksw_coord = '[nup]{2}';
  $ksw_query = 'Q(' . $ksw_sym . $ksw_coord . ')+';
  $ksw_pattern = '/^' . $ksw_query . '$/i';

  $result = preg_match($ksw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return 1;
    }
  }
  return 0;
}

function uptill($max){
  $len = strlen($max);
  if ($len==0) return;
  $main = substr($max,0,1);
  $remain = substr($max,1,$len-1);
  $pattern = '';
  
  switch ($len){
  case 1:
    switch ($main){
    case 0:
      $pattern = '0';
      break;
    case 1:
      $pattern = '[01]';
      break;
      default:
      $pattern = '[0-' . $main . ']';
    }
    break;
  case 2:
    switch ($main){
    case 0:
      $pattern = '0';
      $pattern .= uptill($remain);
      break;
    case 1:
      $pattern = '(0[0-9])|(1[1-' . $remain . '])';
      break;
    default:
      $pattern = '([0-' . ($main-1) . '][0-9])|(' . $main . '[0-' . $remain . '])';
    }
    break;
  }
  return $pattern;
}

function thisup($min){
  $len = strlen($min);
  if ($len==0) return;
  $main = substr($min,0,1);
  $remain = substr($min,1,$len-1);
  $pattern = '';
  
  switch ($len){
  case 1:
    switch ($main){
    case 9:
      $pattern = '9';
      break;
    case 8:
      $pattern = '[89]';
      break;
    default;
      $pattern = '[' . $main . '-9]';
    }
    break;
  case 2:
    switch ($main){
    case 9:
      $pattern = '9';
      break;
    case 8:
      $pattern = '[89]';
      break;
    default:
      $pattern = '[' . $main . '-9]';
    }
    $pattern .= thisup($remain);
    break;
  }
  return $pattern;

}

end stuff to delete!
*/

/**
 * Unicode Integration
 */
function uniord($c) {
    $h = ord($c{0});
    if ($h <= 0x7F) {
        return $h;
    } else if ($h < 0xC2) {
        return false;
    } else if ($h <= 0xDF) {
        return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
    } else if ($h <= 0xEF) {
        return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
                                 | (ord($c{2}) & 0x3F);
    } else if ($h <= 0xF4) {
        return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
                                 | (ord($c{2}) & 0x3F) << 6
                                 | (ord($c{3}) & 0x3F);
    } else {
        return false;
    }
}

function cswRaw($text){
  $csw_sym ='[\x{1D800}-\x{1DA8B}][\x{1DA9A}-\x{1DA9F}][\x{1DAA0}-\x{1DAAF}]';
  $csw_coord = 'n?[0-9]+xn?[0-9]+';
  $csw_word = '(A(' . $csw_sym. ')+)?[LMR](' . $csw_sym . $csw_coord . ')*';
  $csw_punc = $csw_sym;
  $csw_pattern = '/^(' . $csw_word . '|' . $csw_punc . ')( ' . $csw_word . '| ' . $csw_punc .')*$/u';

  $result = preg_match($csw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return 1;
    }
  }
  return 0;
}

function cswLayout($text){
//echo "fail";
  $csw_sym ='[\x{1D800}-\x{1DA8B}][\x{1DA9A}-\x{1DA9F}][\x{1DAA0}-\x{1DAAF}]';
  $csw_coord = 'n?[0-9]+xn?[0-9]+';
  $csw_word = '(A(' . $csw_sym. ')+)?[LMR](' . $csw_coord . ')(' . $csw_sym . $csw_coord . ')*';
  $csw_punc = $csw_sym . $csw_coord;
  $csw_pattern = '/^(' . $csw_word . '|' . $csw_punc . ')( ' . $csw_word . '| ' . $csw_punc .')*$/u';

  $result = preg_match($csw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return 1;
    }
  }
  return 0;
}

function cswDisplay($text){
  $csw_sym ='[\x{1D800}-\x{1DA8B}][\x{1DA9A}-\x{1DA9F}][\x{1DAA0}-\x{1DAAF}]';
  $csw_coord = '[0-9]+x[0-9]+';//always positive
  $csw_word = '[LMR](' . $csw_coord . ')(' . $csw_sym . $csw_ncoord . ')*';
  $csw_punc = $csw_sym . $csw_coord;
  $csw_panel = 'D' . $csw_coord . '(_' . $csw_word . '|_' . $csw_punc .')*';
  $csw_pattern = '/^' . $csw_panel . '( ' . $csw_panel .')*$/u';

  $result = preg_match($csw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return 1;
    }
  }
  return 0;
}

//back and forth conversions
function csw2ksw($csw){
  $pattern ='/[\x{1D800}-\x{1DA8B}][\x{1DA9A}-\x{1DA9F}][\x{1DAA0}-\x{1DAAF}]/u';
  preg_match_all($pattern, $csw,$matches);
  forEach ($matches[0] as $usym){
    $len = strlen($usym);
    $parts = str_split($usym,$len/3);
    $bsw = '';
    foreach ($parts as $uchar){
      $val = strtolower(dechex(uniord($uchar)-hexdec("1d700")));
      $bsw = $bsw . $val;
    }
    $key = 'S' . bsw2key($bsw);
    $csw = str_replace($usym,$key,$csw);
  }
  return $csw;
}

function csw2fsw($csw){
  $pattern ='/n?[0-9]+xn?[0-9]+/';
  preg_match_all($pattern, $csw,$matches);
  forEach ($matches[0] as $str){
    $coord=str2coord($str);
    $fcoord=($coord[0]+500) . 'x' . ($coord[1]+500);
    $csw = str_replace($str,$fcoord,$csw);
  }
  $pattern ='/[\x{1D800}-\x{1DA8B}][\x{1DA9A}-\x{1DA9F}][\x{1DAA0}-\x{1DAAF}]/u';
  preg_match_all($pattern, $csw,$matches);
  forEach ($matches[0] as $usym){
    $len = strlen($usym);
    $parts = str_split($usym,$len/3);
    $bsw = '';
    foreach ($parts as $uchar){
      $val = strtolower(dechex(uniord($uchar)-hexdec("1d700")));
      $bsw = $bsw . $val;
    }
    $key = 'S' . bsw2key($bsw);
    $csw = str_replace($usym,$key,$csw);
  }
  return $csw;
}

function ksw2csw($ksw){
  $pattern = '/S[123][a-f0-9]{2}[012345][a-f0-9]/i';
  preg_match_all($pattern, $ksw,$matches);
  forEach ($matches[0] as $key){
    $bsw = key2bsw($key);
    $usym = bsw2utf($bsw);
    $ksw = str_replace($key,$usym,$ksw);
  }
  return $ksw;
}

function ksw2fsw($ksw){
  //match all symbols...
  $pattern = '/S[123][a-f0-9]{2}[0-5][a-f0-9]n?[0-9]+xn?[0-9]+/i';
  preg_match_all($pattern,$ksw, $matches);
  foreach($matches[0] as $spatial){
    $len = strlen($spatial);
    $str = substr($spatial,6,$len-7+1);
    $coord = str2coord($str);
    $ksw = str_replace($str,($coord[0]+500) . 'x' . ($coord[1]+500),$ksw);
  }
  $pattern = '/[LMR]n?[0-9]+xn?[0-9]+/i';
  preg_match_all($pattern,$ksw, $matches);
  foreach($matches[0] as $spatial){
    $len = strlen($spatial);
    $str = substr($spatial,1,$len-1);
    $coord = str2coord($str);
    $ksw = str_replace($str,($coord[0]+500) . 'x' . ($coord[1]+500),$ksw);
  }
  return $ksw;
}

function fsw2ksw($fsw){
  $pattern = '/[0-9]{3}x[0-9]{3}/i';
  preg_match_all($pattern,$fsw, $matches);
  foreach($matches[0] as $str){
    $coord = str2coord($str);
    $coord[0] -= 500;
    $coord[1] -= 500;
    $fsw = str_replace($str,coord2str($coord[0],$coord[1]) ,$fsw);
  }
  return $fsw;
}

function crosshairs ($ksw,$adj){
    $cluster = ksw2cluster($ksw);
    $min = cluster2min($cluster);
    $max = str2coord($cluster[0][1]);
    if (is_array($adj)){
      $moveX = ($min[0]-$adj[0]);
      $moveY = ($min[1]-$adj[1]);
    } else {
      $moveX = $min[0];
      $moveY = $min[1];
    }

    $clusterW = $max[0]-$min[0];
    $clusterH = $max[1]-$min[1];
    $zeroX = max(-$minX,$clusterW+$minX);
    $zeroY = max(-$minY,$clusterH+$minY);

    $out = 20;
    //add top mark
    $x = $moveX-1;
    $y = $moveY - $zeroY - 12 - $out;
    $spatial = array("S37c00",coord2str($x,$y));
    $cluster[]=$spatial;
    //add bottom mark
    $x = $moveX-1;
    $y = $moveY + $zeroY + $out ;
    $spatial = array("S37c00",coord2str($x,$y));
    $cluster[]=$spatial;

    //add left mark
    $x= $moveX - $zeroX - 12 - $out;
    $y= $moveY-1;
    $spatial = array("S37c06",coord2str($x,$y));
    $cluster[]=$spatial;
    //add right mark
    $x = $moveX + $zeroX + $out;
    $y = $moveY-1;
    $spatial = array("S37c06",coord2str($x,$y));
    $cluster[]=$spatial;

    $ksw = cluster2ksw($cluster);
    $ksw = raw2ksw($ksw);
    return $ksw;
}

function query2ksw($qsearch){
  if (!fswQuery($qsearch)) return;
  $fsw_sym = 'S[123][a-f0-9]{2}[012345u][a-f0-9u]';
  $fsw_coord = '[0-9]{3}x[0-9]{3}';
  $fsw_query = $fsw_sym . $fsw_coord;
  $fsw_pattern = '/' . $fsw_query . '/i';

  $result = preg_match_all($fsw_pattern,$qsearch,$matches);
  $raw = 'M';
  $minX = 0;
  $minY = 0;
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
    $raw .= 'S' . $base . $fill . $rotate . coord2str($x,$y);
  }
  $ksw='';
  if ($raw!="M"){
    global $sym_sizer;
    if(!$sym_sizer) sym_init();
    $sym_sizer->load($raw);
    $expanded = $sym_sizer->expand($raw);

    $ksw_sym = 'S[123][a-f0-9]{2}[012345][a-f0-9]';
    $ksw_coord = 'n?[0-9]+xn?[0-9]+';
    $ksw_syms = $ksw_sym . $ksw_coord . 'x' . $ksw_coord;
    $ksw_pattern = '/' . $ksw_syms . '/i';
    preg_match_all($ksw_pattern,$expanded, $matches);
    $Xmax=array();
    $Ymax=array();
    foreach($matches[0] as $spatial){
      $len = strlen($spatial);
      $nums = explode('x', substr($spatial,6,$len-6));
      $size = str2coord($nums[0] . 'x' . $nums[1]);
      $place = str2coord($nums[2] . 'x' . $nums[3]);
      $Xmax[] = $place[0] + $size[0];
      $Ymax[] = $place[3] + $size[1];
    }
    $ksw = str_replace("M","M" . coord2str(max($Xmax),max($Ymax)),$raw);
  }
  
  return $ksw;
}

function query2iswa($qsearch){
  if (!fswQuery($qsearch)) return;
  $fsw_sym = 'S[123][a-f0-9]{2}[012345u][a-f0-9u]';
  $fsw_pattern = '/' . $fsw_sym . '/i';

  $result = preg_match_all($fsw_pattern,$qsearch,$matches);
  $iswa = '';
  foreach ($matches[0] as $part){
    $iswa .= $part;
  }
  
  return $iswa;
}

function query2ranges($qsearch){
  if (!fswQuery($qsearch)) return;
  $fsw_pattern = 'R[123][a-f0-9]{2}t[123][a-f0-9]{2}';
  $fsw_pattern = '/' . $fsw_pattern . '/i';

  $result = preg_match_all($fsw_pattern,$qsearch,$matches);
  $ranges = '';
  foreach ($matches[0] as $part){
    $ranges .= $part;
  }
  return $ranges;
}

function iswa2list($iswa){

  $fsw_sym = 'S[123][a-f0-9]{2}[012345u][a-f0-9u]';
  $fsw_pattern = '/' . $fsw_sym . '/i';

  $result = preg_match_all($fsw_pattern,$qsearch,$matches);
  foreach ($matches[0] as $part){
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
  return $iswa;
}
function query2syms($qsearch){
  if (!fswQuery($qsearch)) return;
  $fsw_sym = 'S[123][a-f0-9]{2}[012345u][a-f0-9u]';
  $fsw_coord = '([0-9]{3}x[0-9]{3})?';
  $fsw_query = '(' . $fsw_sym . $fsw_coord . ')+';
  $fsw_pattern = '/' . $fsw_query . '/i';
  $result = preg_match($fsw_pattern,$qsearch,$matches);
  if ($result) {
    $orig = $matches[0];
  } else {
    return;
  }
  
  $new = '';
  $result = preg_match_all('/' . $fsw_sym . '/',$qsearch,$matches);
  foreach ($matches[0] as $part){
    $base = substr($part,1,3);
    $fill = substr($part,4,1);
    $rotate = substr($part,5,1);
    $new .= 'S' . $base . $fill . $rotate;
  }
  $qsearch = str_replace($orig,$new,$qsearch);
  return $qsearch;
}
?>
