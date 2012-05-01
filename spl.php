<?php
/**
 * SignPuddle Legacy support
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
 * @brief legacy support for SignPuddle alternate encodings
 * @file
 *   
 */

/**
 * primary compatibility SignPuddle library
 */
function swml2ksw($source){
  $tree = GetXMLTree($source);
  $signCount=count($tree['SWML'][0]['SIGN']);
  $bsw = '';
  for ($i=0;$i<$signCount;$i++){
    $signData=$tree['SWML'][0]['SIGN'][$i];
    $lane = $signData[ATTRIBUTES][LANE];
//    $gloss = $signData[ATTRIBUTES][GLOSS];
    $ksw = '';
    switch($lane){
      case -1:
        $ksw = " L";
        break;
      case 0:
        $ksw = " M";
        break;
      case 1:
        $ksw = " R";
    }
    foreach ($signData['SYMBOL'] as $symbols) {
      $key = id2key($symbols[VALUE],1);
      $strnum = koord2str($symbols[ATTRIBUTES][X], $symbols[ATTRIBUTES][Y]);
      $ksw .= "S" . $key . $strnum;
    }
  }
  $ksw = raw2ksw(trim($ksw));
  return $ksw;
}


function lst2ksw($list){
  $units = array();
  $list = explode("\n",$list);
  foreach($list as $bld){
    $units[] = bld2ksw($bld);
  }
  return implode($units,' ');
}

function bld2ksw($bld){
  global $sym_sizer;
  if (!$bld){return;}
  $build = explode(',',$bld);
  $cnt = count($build);
  $cnt = $cnt - ($cnt%3);
  $keys=array();
  $xs=array();
  $ys=array();
  for ($i=0;$i<$cnt;$i++){
    $sym=$build[$i];
    $key = id2key($sym);
    if ($key) {
      $keys[]=$key;
      $i++;
      $xs[]=$build[$i];
      $i++;
      $ys[]=$build[$i];
    } else {//ignore
      $i++;
      $i++;
    }
  }
  $i++;
  $lane = $build[$i];
  //now determine punc or control char
  if (isPunc($keys[0])){
    $data = 'S' . $keys[0];
  } else { //determine lane
    switch ($lane){
    //written using bsw 3, rather than bsw 3c structure markers
      case 0:
        $data .= 'M';
        break;
      case -1:
        $data .= 'L';
        break;
      case 1:
        $data .= 'R';
        break;
      default:
        echo ("what lane is this?" . $lane);
        die("what lane is this?" . $lane);
    }
    $cnt = count($keys);
    for ($i=0;$i<$cnt;$i++){
      if (!isPunc($keys[$i])){
        $data .= 'S' . $keys[$i];
        $data .= koord2str($xs[$i],$ys[$i]);
      }
    }
  }

  return raw2ksw($data);
}

/**
 * from IMWA symbol id to ISWA 2008 symbol id
 */
$imwa = array();
function imwa2iswa($imwa_id){
  global $imwa;
  if (count($imwa)==0){
    $filename = 'iswa/data/imwa_iswa.txt';
    $contents = trim(file_get_contents($filename));
    $rows = explode("\n",$contents);
    foreach ($rows as $i => $row){
      $parts = explode(',',$row);
      $imwa[$parts[0]] = $parts[1];
    }
  }
  return $imwa[$imwa_id];
}

/**
 * from ISWA 2008 symbol id to ISWA 2010 symbol id
 */
$iswa_08 = array();
function iswa_08_10($iswa_08_id){
  global $iswa_08;
  if (count($iswa_08)==0){
    $filename = 'iswa/data/iswa_08_10.txt';
    $contents = trim(file_get_contents($filename));
    $rows = explode("\n",$contents);
    foreach ($rows as $i => $row){
      $parts = explode(',',$row);
      $iswa_08[$parts[0]] = $parts[1];
    }
  }
  return $iswa_08[$iswa_08_id];
}

function ksw2bld($ksw,$force){
  if(!$ksw) return;
  if (!$force) die("ksw2bld");
  if (!kswLayout($ksw) && !isPunc($ksw)) die("invalid word " . $ksw);
  $cluster=ksw2cluster($ksw);
  $lane = $cluster[0][0];
  $max = str2koord($cluster[0][1]);
  $min = cluster2min($cluster);

  $adjX = intval((250 - ($max[0] - $min[0]))/2) - $min[0];
  $adjY = intval((250 - ($max[1] - $min[1]))/2) - $min[1];
  
//  echo "From " . $min[0] . ',' . $min[1] . ' with ' . $max[0] . ',' . $max[1];
  $bld = array();
  for ($i=1;$i<count($cluster);$i++){
    $key = $cluster[$i][0];
    $coord = str2koord($cluster[$i][1]);
    $bld[] = key2id($key,$force);
    $bld[] = $coord[0] + $adjX;
    $bld[] = $coord[1] + $adjY;

  }
  if ($lane=="L") {
    $bld[] = '';
    $bld[] = -1;
  } else if ($lane=="R") {
    $bld[] = '';
    $bld[] = 1;
  } else {
    //middle lane ignore
  }
  return implode(',',$bld);
}

function ksw2lst($ksw,$force){
  if (!$force) die("ksw2lst");
  $words = explode(" ",$ksw);
  $blds = array();
  foreach ($words as $word){
    if(!$word) continue;
    $blds[] = ksw2bld($word,1);
  }
  return implode("\n",$blds);
}

function puddle_spf(){
  global $ui;
  global $sgn;
  if ($ui and $sgn){
    $type='sgn';
    $id = $sgn;
  } else {
    $type='ui';
    if ($ui) {
      $id=$ui;
    } else if ($sgn) {
      $type='ui';
      $id=$sgn;
    }
  }
  return get_spml($type,$id);
}
?>
