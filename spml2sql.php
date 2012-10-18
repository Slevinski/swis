<?php
$type = $_REQUEST['type'];
$id = $_REQUEST['id'];

$file = 'data/spml/' . $type . $id . '.spml';
$file_sql = 'data/sql/' . $type . $id . '.sql';
if(!file_exists($file)){
  die('invalid type ' . $type . ' and id ' . $id);
}

$tab_pre = $type . '_' . $id . '_';
$tab_entry = $tab_pre . 'entry';
$tab_item = $tab_pre . 'item';
$tab_term = $tab_pre . 'term';

$xml = simplexml_load_file($file);

//sql header
$output = <<<EOT
-- SPML to Puddle database creation
-- version 1.0
-- author Stephen E Slevinski Jr
-- http://www.signpuddle.net

--
-- Database: `Puddle`
--

-- --------------------------------------------------------


EOT;

$output .= <<<EOT
--
-- Table structure for table `$tab_entry`
--
DROP TABLE IF EXISTS `$tab_entry`;

CREATE TABLE IF NOT EXISTS `$tab_entry` (
  `e_id` int unsigned NOT NULL,
  `cdt` int unsigned NOT NULL,
  `mdt` int unsigned NOT NULL,
  PRIMARY KEY  (`e_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `$tab_item`
--
DROP TABLE IF EXISTS `$tab_item`;

CREATE TABLE IF NOT EXISTS `$tab_item` (
  `i_id` int unsigned NOT NULL,
  `cdt` int unsigned NOT NULL,
  `mdt` int unsigned NOT NULL,
  `e_id` int unsigned NOT NULL,
  `lang` varchar(18),
  `txt` text,
  `src` text,
  PRIMARY KEY  (`i_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `$tab_term`
--
DROP TABLE IF EXISTS `$tab_term`;

CREATE TABLE IF NOT EXISTS `$tab_term` (
  `t_id` int unsigned NOT NULL,
  `i_id` int unsigned NOT NULL,
  `trm` text,
  PRIMARY KEY  (`t_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOT;


foreach($xml->children() as $entry) {

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
  $output .= $e_attr['e_id'] . ", ";
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
  
    $src = $detail['src'];
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
?>
