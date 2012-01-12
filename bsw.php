<?php
/**
 * BSW Library for PHP
 * 
 * Copyright 2007-2010 Stephen E Slevinski Jr
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
 * @copyright 2007-2010 Stephen E Slevinski Jr 
 * @author Steve (slevin@signpuddle.net)  
 * @license http://www.opensource.org/licenses/gpl-3.0.html GPL
 * @access public
 * @package SWIS 
 * @version 2.0
 * @filesource
 *   
 */

//base to directory
if(1==0){
  for ($i=0;$i<652;$i++){
    $base = dechex($i+256);
    $view = base2view($base);
    $infile = 'iswa/svg1/' . $base . '/' . $view . '.svg';
    $outfile = 'iswa/everson/base_' . str_pad(1+$i,3,'0',STR_PAD_LEFT) . '.svg';
    copy ($infile,$outfile);
  }
}

//set_time_limit(0);

/**
 * Binary SignWriting for plain text symbol encoding
 *   bsw = 3 digit strings of 3
 *   key = 6 digit string starting with S, 5 without
 *   uni = Unicode string of 3 plane 1 characters
 * 
 */
 
//test strings in ksw
$sym_write = 'S10001';

/**
 * Binary SignWriting for plain text symbol encoding
 *   bsw = 3 digit strings of 3
 *   key = 6 digit string starting with S, 5 without
 *   uni = Unicode string of 3 plane 1 characters
 * 
 */
//base section
function bsw2base($bsw){
  $bsw_base = '';
  $chars = str_split($bsw,3);
  sort($chars,SORT_STRING);
  forEach($chars as $char){
    if(isISWA($char)){
      $bsw_base .= $char;
    }
  }
  return $bsw_base;
}

function base2view($base){
  $view = $base . '00';
  if (isHand($base)){
    if(!isSymGrp($base)){
      $view = $base . '10';
    }
  }
  return $view;
}

//group section
$sg_colorize = array('0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'FF0099', '006600', '006600', '006600', '006600', '006600', '000000', '000000', '884411', 'FF9900');
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

$sg_list = array('100','10e','11e','144','14c','186','1a4','1ba','1cd','1f5', '205','216','22a','255','265','288','2a6','2b7','2d5','2e3', '2f7', '2ff','30a','32a','33b','359', '36d','376', '37f', '387');
function isSymGrp($char){
  $sg_list = array('100','10e','11e','144','14c','186','1a4','1ba','1cd','1f5', '205','216','22a','255','265','288','2a6','2b7','2d5','2e3', '2f7', '2ff','30a','32a','33b','359', '36d','376', '37f', '387');
  return in_array($char,$sg_list);
}


//cat section
$cat_list = array('100','205','2f7', '2ff','36d','37f','387');
function base2cat($base){
  $cat_list = array('100','205','2f7', '2ff','36d','37f','387');
  foreach ($cat_list as $cat){
    if (hexdec($base)==hexdec($cat)) return $cat;
    if (hexdec($base)<hexdec($cat)) return $prev;
    $prev = $cat;
  }
  return $cat;
}

//symbol section
//symbol section
$BaseSymbols = array();
function validkey($key){
  global $BaseSymbols;
  if (count($BaseSymbols)==0) $BaseSymbols = LoadBaseSymbols();
  $key = str_replace("S","",$key);
  $len = strlen($key);
  if ($len<3){ return ;}//error
  $hcode = substr($key,0,3);

  $bs = $BaseSymbols[$hcode];
  if (!$bs){return;}

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

function key2bsw($key){
  $key = str_replace('S','',$key);
  $base = substr($key,0,3);
  $fill = substr($key,3,1);
  $rot = substr($key,4,1);
  return $base . fill2char($fill) . rot2char($rot);
}

function bsw2key($bsw){
  $bsw = str_replace(' ','',$bsw);
  $bsw = str_replace('_','',$bsw);
  $base = substr($bsw,0,3);
  $fill = substr($bsw,3,3);
  $rot = substr($bsw,6,3);
  return $base . char2fill($fill) . char2rot($rot);
}

function key2code($key){
  $key = str_replace('S','',$key);
  return ((hexdec(substr($key,0,3)) - 256) * 96) + ((hexdec(substr($key,3,1)))*16) + hexdec(substr($key,4,1))+1;
}

function code2key($code){
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

function bsw2code($sym){
  return ((hexdec(substr($sym,0,3)) - 256) * 96) + ((hexdec(substr($sym,3,3))-922)*16) + hexdec(substr($sym,6,3))-927;
}

function code2bsw($code){
  $base = intval($code/96);
  $code = $code - $base*96;
  $fill = intval(($code-1)/16);
  $rot = $code - ($fill*16);
  return dechex($base+256) . dechex(($fill-1)+923) . dechex($rot+927);
}


function char2fill($char){
  return dechex(hexdec($char)-922);
}

function fill2char($fill){
  return dechex(hexdec($fill)+922);
}

function char2rot($char){
  return dechex(hexdec($char)-928);
}

function rot2char($rot){
  return dechex(hexdec($rot)+928);
}

//x-iswa-2010 section
function inHexRange($start, $end, $char){
  $char = substr(str_replace('S','',$char),0,3);
  if (hexdec($char)>=hexdec($start) and hexdec($char)<=hexdec($end)){
    return true;
  } else {
    return false;
  }
}

/**
 * ISWA 2010 Section
 */
function isISWA($char){
  return inHexRange("100","38b",$char); 
}
function isWrit($char){
  return inHexRange("100","37e",$char); 
}
function isHand($char){
  return inHexRange("100","204",$char); 
}
function isMove($char){
  return inHexRange("205","2f6",$char); 
}
function isDyn($char){
  return inHexRange("2f7","2fe",$char); 
}
function isHead($char){
  return inHexRange("2ff","36c",$char); 
}
function isTrunk($char){
  return inHexRange("36d","375",$char); 
}
function isLimb($char){
  return inHexRange("376","37e",$char); 
}
function isLoc($char){
  return inHexRange("37f","386",$char); 
}
function isPunc($char){
  return inHexRange("387","38b",$char); 
} 
function isFill($char){
  return inHexRange("39a","39f",$char); 
} 
function isRot($char){
  return inHexRange("3a0","3af",$char); 
} 

/**
 * ISWA 2010 SymbolGroup and BaseSymbol data
 */
function loadSymbolGroups(){
  $filename = 'iswa/data/iswa.sgd';
  $data = file_get_contents($filename);
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
 * Number section
 */
function coord2str($x,$y){
  $str = '';
  if ($x<0) $str .= 'n';
  $str .= abs($x);
  $str .= 'x';
  if ($y<0) $str .= 'n';
  $str .= abs($y);
  return $str;
}

function str2coord($str) {
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
 * Symbol size section
 */
class SYM_SIZE {
  private $db;
  private $symload;
  private $symsize;

  public function __construct() {
    $this->symload = array();
    $this->symsize = array();
    $this->db = sqlite_open('iswa/data/iswa.db2');
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
    $codes = array();
    foreach ($syms as $key){
      $this->symload[] = $key;
      //determine code...
      $codes[]=key2code($key);
    }
    $query = 'select sym_code, sym_w, sym_h from symbol where sym_code in (' . implode($codes,',') . ')';
    $result = sqlite_array_query($this->db, $query);
    foreach($result as $row){
      $this->symsize[code2key($row['sym_code'])] = $row;
    }
  }
  
  public function size($key){
    return $this->symsize[$key];
  }
  
  public function expand($ksw){
    foreach($this->symsize as $key=>$row){
      $strnum = coord2str($row['sym_w'],$row['sym_h']);
      $ksw = str_replace('S' . $key,'S' . $key . $strnum . 'x', $ksw);
    }
    return $ksw;
  }
  
  public function other($ksw){
    
    $bsw3 = str_replace(' ' , '', $bsw3);
    $bsw = ''; //BSW revision 3 draft C
    $this->load($bsw3);
    $chars = str_split($bsw3,3);
    for ($i=0; $i<count($chars); $i++) {
      $char = $chars[$i];
      if (hexdec($char)>=hexdec("387") and hexdec($char)<=hexdec("38b")){//isPunc
        //add punc and io
        $iswa = $char;
        $i++;
        $iswa .= $chars[$i];
        $i++;
        $iswa .= $chars[$i];
        $bsw .= $iswa;
        //add min x,y
        $size = $this->size($iswa);
        $bsw .= num2hex(-intval($size['sym_w']/2));// adding x
        $bsw .= num2hex(-intval($size['sym_h']/2));// adding y
      } else {//a sign...
        //still have $char...  now convert to new range for fa and fc
        switch ($char){ //reorder sign boxes...
          case "0fa":
            $first_char = "0fb";
            break;
          case "0fb":
            $first_char = "0fc";
            break;
          case "0fc":
            $first_char = "0fd";
            break;
        }

        //zero all and center (max & min)
        
        unset($min_X, $min_Y, $max_X, $max_Y);
        unset($cmin_X, $cmin_Y, $cmax_X, $cmax_Y);

        $char = $chars[++$i];//next char is non punc iswa, seq, or box
        $syms = array();
        //cycle through ISWA until Q or P or LBR
        while(hexdec($char)>=hexdec("100") and hexdec($char)<=hexdec("37e")){//is non punc&seq ISWA
          $iswa = $char;
          $i++;
          $iswa .= $chars[$i];
          $i++;
          $iswa .= $chars[$i];
          $i++;
//        echo $iswa . ' ';
          $x = hex2num($chars[$i]);
          $i++;
          $y = hex2num($chars[$i]);
          $size = $this->size($iswa);
          $w = $size['sym_w'];
          $h = $size['sym_h'];
          if (isset($min_X)){
            $min_X = min($min_X,$x);
            $min_Y = min($min_Y,$y);
            $max_X = max($max_X,$x+$w);
            $max_Y = max($max_Y,$y+$h);
          } else {
            $min_X = $x;
            $min_Y = $y;
            $max_X = $x+$w;
            $max_Y = $y+$h;
          }

          if(hexdec($char)>=hexdec("36d") and hexdec($char)<=hexdec("37e")){//is trunk or limb for centering
            if (isset($cmin_X)){
              $cmin_X = min($cmin_X,$x);
              $cmin_Y = min($cmin_Y,$y);
              $cmax_X = max($cmax_X,$x+$w);
              $cmax_Y = max($cmax_Y,$y+$h);
            } else {
              $cmin_X = $x;
              $cmin_Y = $y;
              $cmax_X = $x+$w;
              $cmax_Y = $y+$h;
            }
          }
          $syms[] = $iswa;
          $coords[] = array($x,$y);
          
          $char = @$chars[++$i];//next char is non punc iswa, seq, or box
        }

        //check the center...
        if (isset($cmin_X)){
          $c_X = intval(($cmin_X + $cmax_X)/2);
          $c_Y = intval(($cmin_Y + $cmax_Y)/2);
        } else { //all center
          $c_X = intval(($min_X + $max_X)/2);
          $c_Y = intval(($min_Y + $max_Y)/2);
        }
        //adjust max XY;
        $max_X -= $c_X;
        $max_Y -= $c_Y;

        //now check for sequence first...
        if ($char=="0fd"){
          $bsw .="0fa";
          $char = $chars[++$i];//next char is non punc iswa, seq, or box
          while(hexdec($char)>=hexdec("100") and hexdec($char)<=hexdec("37e")){//is non punc&seq ISWA
            $iswa = $char;
            $i++;
            $iswa .= $chars[$i];
            $i++;
            $iswa .= $chars[$i];
            $i++;
            $char = $chars[$i];//next char is non punc iswa, seq, or box
            $bsw .= $iswa;
          }
        }
        //add signbox
        $bsw .= $first_char;
        $bsw .= num2hex($max_X);
        $bsw .= num2hex($max_Y);
        foreach ($syms as $j => $iswa){
          $coord = $coords[$j];
          $bsw .= $iswa;
          $bsw .= num2hex($coord[0] - $c_X);
          $bsw .= num2hex($coord[1] - $c_Y);
        }
        
        //check for sequence
        
        //cycle through 
        $i--; //back up
      }
    }
    return $bsw;
  }
}


/**
 * Unicode Proposed Integration
 */
function dec2utf($code,$plane=1){
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

function char2utf($char,$plane=1){
  $code = hexdec($char)+55040;//primary shift
  return dec2utf($code,$plane);
}

function char2unicode($char,$plane=1){
  $code = hexdec($char)+55040;//primary shift
  return strtoupper(dechex($plane) . dechex($code));
}

function bsw2utf($bsw, $plane=1){
  $bsw_utf = '';
  $chars = str_split($bsw,3);
  forEach($chars as $char){
    $bsw_utf .= char2utf($char,$plane);
  }
  return $bsw_utf;
}

function utf2char($unichar){
  $chars = str_split($unichar,2);
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
  }

  $code = $c + $b*64 + $a * 64 * 64 - 55040;
  return dechex($code);
}

function utf2bsw($bsw_utf){
  $bsw = '';
//  $pattern ='/[\x{1d800}-\x{1dcff}][\x{fd800}-\x{fdcff}][\x{10d800}-\x{10dcff}]/u';
  $pattern ='/[\x{1D800}-\x{1DCFF}]/u';
  preg_match_all($pattern, $bsw_utf,$matches);
  forEach ($matches[0] as $uchar){
    $val = unpack("N",$uchar);
    $val = dechex($val[1]);
    $bsw = $bsw . utf2char($val);
  }
  return $bsw;
}


?>
