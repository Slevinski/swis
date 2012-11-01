<?php
/**
 * Filesystem Font Library
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
 * @brief Database font functions for the ISWA 2010
 * @file
 *   
 */
 
 
 /** 
 * @brief returns array of symbol group information
 * @return array of symbol group data
 * @ingroup key
 */
function loadSymbolGroups(){
  $filename = 'iswa/data/iswa.sgd';
  $data =  file_get_contents($filename);
  $rows = explode("\n",$data);
  $SymbolGroups = array();
  foreach ($rows as $i => $row){
    $sg = array();
    if ($i==0){
      $keys = explode("\t",$rows[$i]);
    } else {
      $values = explode("\t",$rows[$i]);
      foreach ($keys as $i => $key){
        $sg[$key] = $values[$i];
      }
      $code = $sg['code'];
      $SymbolGroups[$code] = $sg;
    }
  }
  return $SymbolGroups;
}

/** 
 * @brief returns array of base symbol information
 * @return array of base symbol data
 * @ingroup key
 */
function loadBaseSymbols(){
  $filename = 'iswa/data/iswa.bsd';
  $data = file_get_contents($filename);
  $rows = explode("\n",$data);
  $BaseSymbols = array();
  foreach ($rows as $i => $row){
    if ($row!=''){
      $bs = array();
      if ($i==0){
        $keys = explode("\t",$rows[$i]);
      } else {
        $values = explode("\t",$rows[$i]);
        foreach ($keys as $i => $key){
          $bs[$key] = $values[$i];
        }
        $code = $bs['code'];
        $BaseSymbols[$code] = $bs;
      }
    }
  }
  return $BaseSymbols;
}

/**
 * translation between ISWA 2010 symbol id and symbol key
 */
$idkey = array();
function load_iswa_id_key(){
  global $idkey;
  
  $filename = 'iswa/data/iswa_sym_base.txt';
  $contents = trim(file_get_contents($filename));
  $rows = explode("\n",$contents);
  foreach ($rows as $i => $row){
    $parts = explode(',',$row);
    $idkey[$parts[0]] = (string)$parts[1];
  }
}

function id2key($sid){
  global $idkey;
  if (count($idkey)==0) load_iswa_id_key();
  $sBase = substr($sid,0,12);
  $base = $idkey[$sBase];
  if (strlen($sid) == 18) {
    $ifill = intval(substr($sid,13,2)) -1;
    $hrot = dechex(intval(substr($sid,16,2) -1));
    $key = $base . $ifill . $hrot;
  } else {
    $key = base2view($base);
  }
  return $key;
}

function key2id($key,$force){
$key=str_replace('S','',$key);
if (!$force) die('key2id');
//return "01-01-001-01-01-01";
  global $idkey;
  if (count($idkey)==0) load_iswa_id_key();
  $base = substr($key,0,3);
  $sym = array_search($base,$idkey,true);
  if (strlen($key)!=5){
    $key = base2view($base);
  }
  $ifill = intval(substr($key,3,1)) + 1;
  $irot = hexdec(strtoupper(substr($key,4,1))) + 1;
  if ($irot>9) {
    $srot = (string)$irot;
  } else {
    $srot = '0' . $irot;
  }
  return $sym . '-0' . $ifill . '-' . $srot;
}

$messages = array();
function load_iswaNames(){
  global $messages;
  include('iswa/data/iswa.i18n.php');
}

function iswaName($id, $lang='en'){
  global $messages;
  if (count($messages)==0) load_iswaNames();
  return $messages[$lang]['iswa_' . $id];
}

function image_png($key,$ver){
  $base = substr($key,0,3);
  $file = 'iswa/png' . $ver . '/' . $base . '/' . $key . '.png';
  return file_get_contents($file);
}

function image_svg($key,$ver){
  $base = substr($key,0,3);
  $file = 'iswa/svg' . $ver . '/' . $base . '/' . $key . '.svg';
  return file_get_contents($file);
}

function image_txt($key,$ver){
  $base = substr($key,0,3);
  $file = 'iswa/txt' . $ver . '/' . $base . '/' . $key . '.txt';
  return file_get_contents($file);
}

global $sym_sizer;
function sym_init(){
  global $sym_sizer;
  $sym_sizer = new SYM_SIZE();
}

class SYM_SIZE {
  private $db;
  private $symload;
  private $symsize;

  public function __construct() {
    $this->symload = array();
    $this->symsize = array();
    $this->db = file_get_contents('iswa/data/iswa.size');
  }

  public function load($ksw){
    
    if ($ksw=="")return;
    $sym_pattern = '/S[123][a-f0-9]{2}[012345][a-f0-9]/i';
    preg_match_all($sym_pattern,$ksw, $matches);
    $syms = array();
    foreach($matches[0] as $spatial){
      $syms[] = substr($spatial,1,5);
    }
    $syms = array_unique($syms);
    $syms = array_diff($syms,$this->symload);
    foreach ($syms as $key){
      $this->symload[] = $key;
      $size_pattern = '/S' . $key . '[0-9]{3}x[0-9]{3}/i';
      preg_match($size_pattern,$this->db, $match);
      $size = str2coord(substr($match[0],6,13));
      $this->symsize[$key] = koord2str($size[0],$size[1]);
    }
  }
  
  public function size($key){
    $key = str_replace('S','',$key);
    return $this->symsize[$key];
  }
  
  public function expand($ksw){
    foreach($this->symsize as $key=>$row){
      $ksw = str_replace('S' . $key,'S' . $key . $row . 'x', $ksw);
    }
    return $ksw;
  }
 
}
?>
