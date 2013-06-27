<?php
set_time_limit(0);
$type = $_REQUEST['type'];
$id = $_REQUEST['id'];
$lang = $_REQUEST['lang'];
$slang = $_REQUEST['slang'];

$file = 'data/spml/' . $type . $id . '.spml';
if(!file_exists($file)){
  die('invalid type ' . $type . ' and id ' . $id);
}

$xml = simplexml_load_file($file);

include "fsw.php";
include "db.php";

$sql = 'INSERT INTO signs (text,lang,created,modified) values ';
$sql .= '(:text,:lang,:created,:modified);';
$sign_stmt = $db->prepare($sql);
$sign_stmt->bindParam(':lang', $lang);

$sql = 'INSERT INTO terms (text,lang,created,modified) values ';
$sql .= '(:text,:lang,:created,:modified);';
$term_stmt = $db->prepare($sql);
$term_stmt->bindParam(':lang', $slang);

$sql = 'INSERT INTO links (sign_id,term_id) values ';
$sql .= '(:sign_id,:term_id);';
$link_stmt = $db->prepare($sql);

foreach($xml->children() as $entry) {
  $sign = '';
  $terms = array();

  $keys=array();//spoken term keys

  $attr = $entry->attributes();
  $cdt = date('Y-m-d H:i:s',intval($attr['cdt']));
  $mdt = date('Y-m-d H:i:s',intval($attr['mdt']));
  $sign_stmt->bindParam(':created', $cdt);
  $sign_stmt->bindParam(':modified', $mdt);
  $term_stmt->bindParam(':created', $cdt);
  $term_stmt->bindParam(':modified', $mdt);

  $entry_id = intval($attr['id']);
//  echo $entry_id;

  foreach($entry->term as $term){
    if (fswText($term)){
      $sign_stmt->bindParam(':text', $term);
      $sign_stmt->execute();
      $sign = $db->lastInsertId();
//      echo "s";
    } else {
      $term_stmt->bindParam(':text', $term);
      $term_stmt->execute();
      $terms[] = $db->lastInsertId();
//      echo "t";
    }
  }

  if ($sign) {
    foreach ($terms as $id){
      $link_stmt->bindParam(':sign_id', $sign);
      $link_stmt->bindParam(':term_id', $id);
      $link_stmt->execute();
//      echo "l";
    }
  }
//  echo "<br>";
}
echo "done";
?>
