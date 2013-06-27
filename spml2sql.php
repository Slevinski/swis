<?php
set_time_limit(0);
$type = $_REQUEST['type'];
$id = $_REQUEST['id'];

$file = 'data/spml/' . $type . $id . '.spml';
if(!file_exists($file)){
  die('invalid type ' . $type . ' and id ' . $id);
}

$xml = simplexml_load_file($file);

include "fsw.php";

$signs = array();
$terms = array();
$links = array();

/*
$val = 5;
$sql = "REPLACE table (column) VALUES (:val)";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':val', $val, PDO::PARAM_INT);
$stmt->execute();
$lastId = $dbh->lastInsertId();
*/

foreach($xml->children() as $entry) {

  $keys=array();//spoken term keys

  foreach($entry->term as $term){
    if (fswText($term)){
      if (in_array($term,$signs)){
        $key = array_search($term,$signs);
      } else {
        $key = count($signs);
        $signs[] = $term;
      }
    } else {
      if (in_array($term,$terms)){
        $keys[] = array_search($term,$terms);
      } else {
        $keys[] = count($terms);
        $terms[] = $term;
      }
    }
  }

  foreach ($keys as $link){
    $links[$key][]=$link;
  }
}

foreach ($links as $key=>$keys){
  echo $key . " links to ";
  foreach ($keys as $link){
    echo $link . ' ';
  }
  echo "<br>";
}
  
/*
  $output .= <<<EOT


-- entry 
INSERT INTO `$tab_entry` (`e_id`, `cdt`, `mdt`) VALUES

EOT;

  $e_attr = $entry->attributes();
  $cdt = $e_attr['cdt'];
  $mdt = $e_attr['mdt'];
  if ($cdt=="" and $mdt==""){
    $cdt=time();
    $mdt=time();
  } else if ($cdt==""){
    $cdt=$mdt;
  } else if ($mdt==""){
    $mdt=$cdt;
  }
  $output .= "(";
//  $output .= $e_attr['e_id'] . ", ";
  $output .= $e_attr['id'] . ", ";
  $output .= $cdt . ", ";
  $output .= $mdt . ")";
  $output.= ";";

  //entry items
  foreach($entry->children() as $item){

    $output .= <<<EOT

INSERT INTO `$tab_item` (`i_id`, `cdt`, `mdt`, `e_id`, `lang`, `txt`, `src`) VALUES

EOT;

    $i_attr = $item->attributes();

    $txt = $item->text[0];
    $src= $item->src[0];
    $cdt = $i_attr['cdt'];
    $mdt = $i_attr['mdt'];
    if ($cdt=="" and $mdt==""){
      $cdt=time();
      $mdt=time();
    } else if ($cdt==""){
      $cdt=$mdt;
    } else if ($mdt==""){
      $mdt=$cdt;
    }
  
//    $src = $detail['src'];
    $output .= "(";
    $output .= $i_attr['i_id'] . ', ';
    $output .= $cdt . ', ';
    $output .= $mdt . ', ';
    $output .= $e_attr['e_id'] . ', ';
    $output .= '"' . $i_attr['lang'] . '", ';
    $output .= '"' . addslashes($txt) . '", ';
    $output .= '"' . addslashes($src) . '");';

    $terms = $item->term;
    foreach ($terms as $term){
      $output .= <<<EOT

INSERT INTO `$tab_term` (`t_id`, `i_id`, `trm`) VALUES

EOT;

      $output .= "(";
      $output .= $term['t_id'] . ', ';
      $output .= $i_attr['i_id'] . ', ';
      $output .= '"' . addslashes($term) . '"); ';

    } 

  }
}

file_put_contents($file_sql,$output);
echo '<a href="' . $file_sql . '">SQL file</a>';
*/
?>
