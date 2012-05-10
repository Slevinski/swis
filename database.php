<?php
/**
 * Database Font Library
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
 * @brief Database font functions for the ISWA 2010
 * @file
 *   
 */

global $iswa_db;
$iswa_db = new PDO('sqlite:' . dirname(__FILE__) . '/iswa.sql3');

 /** 
 * @brief returns array of symbol group information
 * @return array of symbol group data
 * @ingroup key
 */
function loadSymbolGroups(){
  global $iswa_db;
  $SymbolGroups = array();
  $query = 'select * from symbolgroup';
  $result = $iswa_db->query($query);
  foreach($result as $row){
    $code = $row['code'];
    $SymbolGroups[$code] = $row;
  }
  return $SymbolGroups;

}

/** 
 * @brief returns array of base symbol information
 * @return array of base symbol data
 * @ingroup key
 */
function loadBaseSymbols(){
  global $iswa_db;
  $BaseSymbols = array();
  $query = 'select * from basesymbol';
  $result = $iswa_db->query($query);
  foreach($result as $row){
    $code = $row['code'];
    $BaseSymbols[$code] = $row;
  }
  return $BaseSymbols;
}

/**
 * translation between ISWA 2010 symbol id and symbol key
 */
function id2key($sid){
  global $iswa_db;
  $sBase = substr($sid,0,12);
  $cat_num = intval(substr($sid,0,2));
  $grp_num = intval(substr($sid,3,2));
  $bas_num = intval(substr($sid,6,3));
  $var_num = intval(substr($sid,10,2));
  $query = 'select basesymbol.code from basesymbol,symbolgroup where ';
  $query .= 'cat_num = ' . $cat_num . ' and ';
  $query .= 'grp_num = ' . $grp_num . ' and ';
  $query .= 'bas_num = ' . $bas_num . ' and ';
  $query .= 'var_num = ' . $var_num . ' and ';
  $query .= 'sg_code = symbolgroup.code';
  $result = $iswa_db->query($query)->fetch();
  $base = dechex($result[0]);
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
  global $iswa_db;
  $key=str_replace('S','',$key);
  if (!$force) die('key2id');
  $base = hexdec(substr($key,0,3));
  if (strlen($key)!=5){
    $key = base2view($base);
  }
  $ifill = intval(substr($key,3,1)) + 1;
  $irot = hexdec(strtoupper(substr($key,4,1))) + 1;
  $query = 'select cat_num, grp_num, bas_num, var_num from basesymbol, symbolgroup where ';
  $query .= 'basesymbol.code = ' . $base . ' and sg_code = symbolgroup.code';
  $row = $iswa_db->query($query)->fetch();
  
  return sprintf("%02d-%02d-%03d-%02d-%02d-%02d",$row[0],$row[1],$row[2],$row[3],$ifill, $irot);
}

function image_png($key,$ver){
  global $iswa_db;
  $code = key2code($key);
  $query = 'select glyph from font_png' . $ver . ' where code = ' . $code;
  $row = $iswa_db->query($query)->fetch();
  return $row[0];
}

function image_svg($key,$ver){
  global $iswa_db;
  $code = key2code($key);
  $svgtab = 'font_svg' . $ver;
  $query = 'select glyph,w,h from ' . $svgtab . ', symbol where ';
  $query .= $svgtab . '.code = ' . $code;
  $query .= ' and symbol.code = ' . $code;
  $row = $iswa_db->query($query)->fetch();
  
  $svg = <<<EOT
<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN"
 "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">
<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
EOT;

  $svg .= ' width="' . $row['w'] . '" height="' . $row['h'] . '"';
  $svg .= '>' . "\n";

  $svg .= "<metadata>S" . $key . "</metadata>\n";
  $svg .= $row['glyph'];
  $svg .= "\n</svg>";

  return $svg;
}

function image_txt($key,$ver){
  global $iswa_db;
  $code = key2code($key);
  $query = 'select glyph from font_txt' . $ver . ' where code = ' . $code;
  $row = $iswa_db->query($query)->fetch();
  return $row[0];
}

global $sym_sizer;
function sym_init(){
  global $sym_sizer;
  $sym_sizer = new SYM_SIZE();
}

class SYM_SIZE {
  private $symload;
  private $symsize;

  public function __construct() {
    $this->symload = array();
    $this->symsize = array();
  }

  public function load($ksw){
    global $iswa_db;
    if ($ksw=="")return;
    $sym_pattern = '/S[123][a-f0-9]{2}[012345][a-f0-9]/i';
    preg_match_all($sym_pattern,$ksw, $matches);
    $syms = array();
    foreach($matches[0] as $spatial){
      $syms[] = substr($spatial,1,5);
    }
    $syms = array_unique($syms);
    $syms = array_diff($syms,$this->symload);
    $codes = array();
    foreach ($syms as $key){
      $this->symload[] = $key;
      //determine code...
      $codes[]=key2code($key);
    }
    $query = 'select code, w, h from symbol where code in (' . implode($codes,',') . ')';
    $result = $iswa_db->query($query);
    foreach($result as $row){
      $this->symsize[code2key($row['code'])] = $row;
    }
  }
  
  public function size($key){
    $key = str_replace('S','',$key);
    return $this->symsize[$key];
  }
  
  public function expand($ksw){
    foreach($this->symsize as $key=>$row){
      $strnum = koord2str($row['w'],$row['h']);
      $ksw = str_replace('S' . $key,'S' . $key . $strnum . 'x', $ksw);
    }
    return $ksw;
  }
 
}
 ?>
