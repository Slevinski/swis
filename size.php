<?php
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
    $key = str_replace('S','',$key);
    return $this->symsize[$key];
  }
  
  public function expand($ksw){
    foreach($this->symsize as $key=>$row){
      $strnum = koord2str($row['sym_w'],$row['sym_h']);
      $ksw = str_replace('S' . $key,'S' . $key . $strnum . 'x', $ksw);
    }
    return $ksw;
  }
 
}
?>