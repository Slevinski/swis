<?php
set_time_limit(0);
include "fsw.php";
include "db.php";

function db_load($name){
  global $db;
  if (substr($name,0,2)=='ui') {
    $id = substr($name,2);
    if ($id=='') {
      return;
    }  else {
      //user id but convert later...
      $type='ui';
    }
  } else {
    $id = substr($name,3);
    if (!$id) {
      return;
    }  else {
      $type = 'sgn';
    }
  }
  $sql = 'SELECT id,sign_lang, spoken_lang from puddles where namespace="' . $type . '" and ns_id=' . $id . ';';
  $stmt = $db->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetch();
  $puddle_id = $result[0];
  $sign_lang = $result[1];
  $spoken_lang = $result[2];

  $file = 'data/spml/' . $type . $id . '.spml';
  $xml = simplexml_load_file($file);
  
  $sql = 'INSERT INTO entries (puddle_id,sub_id,status_id,source,user,created,modified) values ';
  $sql .= '(:puddle_id,:sub_id,:status_id,:source,:user,:created,:modified);';
  $entry_stmt = $db->prepare($sql);
  $entry_stmt->bindParam(':puddle_id', $puddle_id);
  $entry_stmt->bindValue(':status_id', 1);

  $sql = 'INSERT INTO terms (entry_id,text,lang) values ';
  $sql .= '(:entry_id,:text,:lang);';
  $term_stmt = $db->prepare($sql);

  $sql = 'INSERT INTO texts (entry_id,text,lang) values ';
  $sql .= '(:entry_id,:text,:lang);';
  $text_stmt = $db->prepare($sql);

  foreach($xml->entry as $entry) {
    $sign = '';
    $terms = array();


    $attr = $entry->attributes();
    $cdt = date('Y-m-d H:i:s',intval($attr['cdt']));
    $mdt = date('Y-m-d H:i:s',intval($attr['mdt']));
    $entry_stmt->bindParam(':created', $cdt);
    $entry_stmt->bindParam(':modified', $mdt);
    $entry_stmt->bindParam(':sub_id', $attr['id']);
    $entry_stmt->bindParam(':user', $attr['usr']);

    $source = '';
    foreach($entry->src as $src){
      $source = $src;
    }
    $entry_stmt->bindParam(':source', $source);
    $entry_stmt->execute();
    $entry_id = $db->lastInsertId();
    
    $term_stmt->bindParam(':entry_id', $entry_id);
    $text_stmt->bindParam(':entry_id', $entry_id);

    foreach($entry->term as $term){
      $term_stmt->bindParam(':text', $term);
      if (fswText($term)){
        $term_stmt->bindParam(':lang', $sign_lang);
      } else {
        $term_stmt->bindParam(':lang', $spoken_lang);
      }
      $term_stmt->execute();
    }

    foreach($entry->text as $text){
      $text_stmt->bindParam(':text', $text);
      if (fswText($text)){
        $text_stmt->bindParam(':lang', $sign_lang);
      } else {
        $text_stmt->bindParam(':lang', $spoken_lang);
      }
      $text_stmt->execute();
    }

  }
}


$list = $_REQUEST['list'];
if (!$list){
  $list = implode(',',glob('data/spml/*.spml'));
  $list = str_replace("data/spml/",'',str_replace(".spml",'',$list));
}

if ($list){
  $items = explode(',',$list);
  $name = $items[0];
  unset ($items[0]);
  $list = implode(',',$items);
}

if ($name) db_load($name);
if (!$list){ 
  echo "Finished";
  echo "<hr>";
} else {
  echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=db_load.php?list=' . $list . '">' . "\n";
}

?>
