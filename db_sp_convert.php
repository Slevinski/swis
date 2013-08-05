<?php
set_time_limit(0);
error_reporting(E_ALL);
//step 1: swapable start
  include 'db_init.php';
//  include 'db.php';

include 'fsw.php';

//step 2: 
//include spml files...
$sql = 'INSERT INTO puddles (namespace,ns_id,view_security,add_security,edit_security,register_level,upload_level) values ';
$sql .= '(:namespace,:ns_id,:view_security,:add_security,:edit_security,:register_level,:upload_level);';
$pdl_stmt = $db->prepare($sql);

$sql = 'INSERT INTO entries (puddle_id,sub_id,status_id,source,created,modified) values ';
$sql .= '(:puddle_id,:sub_id,:status_id,:source,:created,:modified);';
$entry_stmt = $db->prepare($sql);

$sql = 'INSERT INTO terms (entry_id,text,lang) values ';
$sql .= '(:entry_id,:text,:lang);';
$term_stmt = $db->prepare($sql);

$sql = 'INSERT INTO texts (entry_id,text,lang) values ';
$sql .= '(:entry_id,:text,:lang);';
$text_stmt = $db->prepare($sql);

$tags = array();
$puddles = array();
foreach(glob('data/spml/*.spml') as $file){
  $namespace = str_replace('data/spml/','',str_replace('.spml','',$file));
  if (substr($namespace,0,2)=='ui') {
    $id = substr($namespace,2);
    if ($id=='') {
      continue;
    }  else {
      //user id but convert later...
      $namespace='ui';
      $entry_stmt->bindValue(':puddle_id',2);
    }
  } else {
    $id = substr($namespace,3);
    if (!$id) {
      continue;
    }  else {
      $namespace = 'sgn';
      $entry_stmt->bindValue(':puddle_id',3);
    }
  }

  $adm = 'data/adm/' . $namespace . $id . '.adm.php';
  if(!file_exists($adm)){
    $view = 0;
    $add = 1;
    $edit = 1;
    $copy = 1;
    $register = 0;
    $upload = 3;
    //echo ('invalid namespace ' . $namespace . ' and id ' . $id . "<br>");
  } else {
    include $adm;
  }
  $xml = simplexml_load_file($file);
  
  $attr = $xml->attributes();
  $cdt = date('Y-m-d H:i:s',intval($attr['cdt']));
  $mdt = date('Y-m-d H:i:s',intval($attr['mdt']));

  $source='';
  foreach($xml->src as $src) {
    $source=$src;
  }

  foreach($xml->png as $png) {
//    echo $png . "<br>";
  }

  $entry_stmt->bindParam(':sub_id',$id);
  $entry_stmt->bindValue(':status_id',1);
  $entry_stmt->bindParam(':source',$source);
  $entry_stmt->bindParam(':created',$cdt);
  $entry_stmt->bindParam(':modified',$mdt);
  $entry_stmt->execute();
  $entry_id = $db->lastInsertId();
  $term_stmt->bindParam(':entry_id',$entry_id);
  $text_stmt->bindParam(':entry_id',$entry_id);

  foreach($xml->term as $term) {
    $term_stmt->bindParam(':text',$term);
    if (fswText($term)){
      $term_stmt->bindValue(':lang','ase');
    } else {
      $term_stmt->bindValue(':lang','en');
    }
    $term_stmt->execute();
  }

  foreach($xml->text as $text) {
    $text_stmt->bindParam(':text',$text);
    if (fswText($text)){
      $text_stmt->bindValue(':lang','ase');
    } else {
      $text_stmt->bindValue(':lang','en');
    }
    $text_stmt->execute();
  }
  $pdl_stmt->bindParam(':namespace',$namespace);
  $pdl_stmt->bindParam(':ns_id',$id);
  $pdl_stmt->bindParam(':view_security',$view);
  $pdl_stmt->bindParam(':add_security',$add);
  $pdl_stmt->bindParam(':edit_security',$edit);
  $pdl_stmt->bindParam(':register_level',$register);
  $pdl_stmt->bindParam(':upload_level',$upload);
  $pdl_stmt->execute();
  $puddles[$namespace . $id] = $db->lastInsertId();
}

//step 3: load users
include 'data/adm/usr.php';
$sql = 'INSERT INTO users (security,name,display,email,password) values ';
$sql .= '(:security,:name,:display,:email,:password);';
$user_stmt = $db->prepare($sql);
foreach ($userlist as $key=>$val){
  $user_stmt->bindParam(':security',$val['security']);
  $user_stmt->bindParam(':name',$key);
  $user_stmt->bindParam(':display', $val['display']);
  $user_stmt->bindParam(':email',$val['email']);
  $user_stmt->bindParam(':password',$val['password']);
  $user_stmt->execute();
  $userlist[$key]['id'] = $db->lastInsertId();
}

//step 4:
//include adm files...
$sql = 'INSERT INTO puddle_users (puddle_id,user_id,security) values ';
$sql .= '(:puddle_id,:user_id,:security);';
$user_stmt = $db->prepare($sql);

foreach(glob('data/adm/*.adm.php') as $file){
  include($file);
  $namespace = str_replace('data/adm/','',str_replace('.adm.php','',$file));
  if (substr($namespace,0,2)=='ui') {
    $id = substr($namespace,2);
    if ($id=='') {
      $puddle_id = 2;
    }  else {
      //user id but convert later...
      $puddle_id = $puddles['ui' . $id];
    }
  } else {
    $id = substr($namespace,3);
    if (!$id) {
      $puddle_id = 3;
    }  else {
      //user id but convert later...
      $puddle_id = $puddles['sgn' . $id];
    }
  }

  $user_stmt->bindParam(':puddle_id',$puddle_id);
  foreach ($localusers as $key=>$val){
    $user_stmt->bindParam(':user_id',$userlist[$key]['id']);
    $user_stmt->bindParam(':security', $val['security']);
    $user_stmt->execute();
  }
}

//step 5:
//load languages
$sql = 'update puddles set sign_lang=:sign_lang, spoken_lang=:spoken_lang, second_lang=:second_lang ';
$sql .= 'where namespace=:namespace and ns_id=:ns_id;';
$lang_stmt = $db->prepare($sql);

$lang_stmt->bindValue(':sign_lang','ase');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
$lang_stmt->bindValue(':namespace','ui');
$lang_stmt->bindValue(':ns_id',1);
$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(1,4,5,28,151,17,21,25,105,111,128,150,35);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ase');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang',null);
$lang_stmt->bindValue(':namespace','ui');
$lang_stmt->bindValue(':ns_id',5);
$lang_stmt->execute();

$lang_stmt->bindValue(':sign_lang','gsg');
$lang_stmt->bindValue(':spoken_lang','de');
$lang_stmt->bindValue(':second_lang','en');
$lang_stmt->bindValue(':namespace','ui');
$lang_stmt->bindValue(':ns_id',8);
$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(53,26,27);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ssr');
$lang_stmt->bindValue(':spoken_lang','fr');
$lang_stmt->bindValue(':second_lang','en');
$lang_stmt->bindValue(':namespace','ui');
$lang_stmt->bindValue(':ns_id',4);
$lang_stmt->execute();
$lang_stmt->bindValue(':ns_id',10);
$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(20,22,49);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','nsl');
$lang_stmt->bindValue(':spoken_lang','no');
$lang_stmt->bindValue(':second_lang','en');
$lang_stmt->bindValue(':namespace','ui');
$lang_stmt->bindValue(':ns_id',3);
$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(23,24,69);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','cse');
$lang_stmt->bindValue(':spoken_lang','cs');
$lang_stmt->bindValue(':second_lang','en');
$lang_stmt->bindValue(':namespace','ui');
$lang_stmt->bindValue(':ns_id',6);
$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(36,37,52);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','pso');
$lang_stmt->bindValue(':spoken_lang','pl');
$lang_stmt->bindValue(':second_lang','en');
$lang_stmt->bindValue(':namespace','ui');
$lang_stmt->bindValue(':ns_id',7);
$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(19,38,39);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','csc');
$lang_stmt->bindValue(':spoken_lang','ca');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',5); //spanish
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(56,94);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ssp');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',5); //spanish
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(55,93);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','sdl');
$lang_stmt->bindValue(':spoken_lang','ar');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',1);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(40,91);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','jos');
$lang_stmt->bindValue(':spoken_lang','ar');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',1);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(86,92);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','jsl');
$lang_stmt->bindValue(':spoken_lang','ja');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',1);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(99,64);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','eth');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',1);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(18,100);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','sfb');
$lang_stmt->bindValue(':spoken_lang','fr');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',1);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(95,43);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','vgt');
$lang_stmt->bindValue(':spoken_lang','nl');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',1);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(98,44);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','sgg');
$lang_stmt->bindValue(':spoken_lang','de');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',1);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(96,48);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','bzs');
$lang_stmt->bindValue(':spoken_lang','pt');
$lang_stmt->bindValue(':second_lang','en');
$lang_stmt->bindValue(':namespace','ui');
$lang_stmt->bindValue(':ns_id',12);
$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(114,116,46);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','mfs');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang','es');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(65,120);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ncs');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(67,119);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','psr');
$lang_stmt->bindValue(':spoken_lang','pt');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(33,115,117);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','dsl');
$lang_stmt->bindValue(':spoken_lang','da');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(30,118);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','hsh');
$lang_stmt->bindValue(':spoken_lang','hu');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(122,123);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','bfi');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(59,125);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','fsl');
$lang_stmt->bindValue(':spoken_lang','fr');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(58,124);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','tse');
$lang_stmt->bindValue(':spoken_lang','fr');
$lang_stmt->bindValue(':second_lang','ar');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(104,126);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','mdl');
$lang_stmt->bindValue(':spoken_lang','mt');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(31,127,147,103);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','rms');
$lang_stmt->bindValue(':spoken_lang','ro');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(132,138,139);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','rsl');
$lang_stmt->bindValue(':spoken_lang','ru');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(88,141,142);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ugy');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(143,144);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','aed');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',12);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(41,145,146);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ysl');
$lang_stmt->bindValue(':spoken_lang','sl');
$lang_stmt->bindValue(':second_lang','en');
$lang_stmt->bindValue(':namespace','ui');
$lang_stmt->bindValue(':ns_id',13);
$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(74,148,149);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','afg');
$lang_stmt->bindValue(':spoken_lang','ar');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(106);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','lls');
$lang_stmt->bindValue(':spoken_lang','lt');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(107);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','lsl');
$lang_stmt->bindValue(':spoken_lang','lv');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(108);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','eso');
$lang_stmt->bindValue(':spoken_lang','et');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(109);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','isr');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(110);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','gsm');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(112);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ase');
$lang_stmt->bindValue(':spoken_lang','ht');
$lang_stmt->bindValue(':second_lang','fr');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(113);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','pys');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(129);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ukl');
$lang_stmt->bindValue(':spoken_lang','uk');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(130);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','icl');
$lang_stmt->bindValue(':spoken_lang','is');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(131);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','nsp');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(133);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','bqn');
$lang_stmt->bindValue(':spoken_lang','bg');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(134);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','csg');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(135);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ecs');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(136);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','esn');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(137);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','fcs');
$lang_stmt->bindValue(':spoken_lang','fr');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(47,81,140);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','hds');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(16);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','asq');
$lang_stmt->bindValue(':spoken_lang','de');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(29);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','nsi');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(32);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','tsq');
$lang_stmt->bindValue(':spoken_lang','th');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(34);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','asf');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(42);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ils');
$lang_stmt->bindValue(':spoken_lang','eo');
$lang_stmt->bindValue(':second_lang','en');
$lang_stmt->bindValue(':namespace','ui');
$lang_stmt->bindValue(':ns_id',11);
$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(54);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','bvl');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(45);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','slf');
$lang_stmt->bindValue(':spoken_lang','it');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(50);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','csn');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(51);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','fse');
$lang_stmt->bindValue(':spoken_lang','fi');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(57);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','bfi-IE');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(60);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','gss');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(61);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','isg');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(62);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ise');
$lang_stmt->bindValue(':spoken_lang','it');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(63);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','xml');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(66);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','dse');
$lang_stmt->bindValue(':spoken_lang','nl');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(68);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','nzs');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(70);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','prl');
$lang_stmt->bindValue(':spoken_lang','es');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(71);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','psp');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(72);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','swl');
$lang_stmt->bindValue(':spoken_lang','sv');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(73);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','tss');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(75);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','vsl');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(76);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','sfs');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(77);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','kvk');
$lang_stmt->bindValue(':spoken_lang','ko');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(78);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','xki');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(79);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','kvk');
$lang_stmt->bindValue(':spoken_lang','zh');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(83);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','esl');
$lang_stmt->bindValue(':spoken_lang','ar');
$lang_stmt->bindValue(':second_lang','en');
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(84);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','ins');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(85);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','pks');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(87);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','svk');
$lang_stmt->bindValue(':spoken_lang','sk');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(89);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','tsm');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(90);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}

$lang_stmt->bindValue(':sign_lang','bxy');
$lang_stmt->bindValue(':spoken_lang','en');
$lang_stmt->bindValue(':second_lang',null);
//$lang_stmt->bindValue(':namespace','ui');
//$lang_stmt->bindValue(':ns_id',13);
//$lang_stmt->execute();
$lang_stmt->bindValue(':namespace','sgn');

$ids = array(82);
foreach($ids as $id){
  $lang_stmt->bindParam(':ns_id',$id);
  $lang_stmt->execute();
}
echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=db_load.php">' . "\n";

?>
