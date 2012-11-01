<?php
//for testing
//include 'msw.php';


function typeid($str){
//spml files are named with alpha chracters followed by digits, ex: ui1 sgn28, ...
  preg_match('/(?P<type>\w+?)(?P<id>\d+)/', $str, $matches);
  $type = @$matches['type'];
  $id = @$matches['id'];
  $typeid = array();
  $typeid['str'] = $str;
  $typeid['type'] = $type;
  $typeid['id'] = $id;
  $nstr = $type . $id;
  $typeid['valid'] = ($str == $nstr);

  return $typeid;
}

function humanfilesize($file) {
  // Adapted from: http://www.php.net/manual/en/function.filesize.php
  $size = filesize($file);
  $mod = 1024;
  $units = explode(' ','B KB MB GB TB PB');
  for ($i = 0; $size > $mod; $i++) {
    $size /= $mod;
  }
  return round($size, 2) . ' ' . $units[$i];
}

function get_spml($type,$pid){
  global $data;
  set_time_limit(0);
  if ($type.$pid == '') return '';
  $filename = 'data/spml/' . $type . $pid . '.spml';
  if (!file_exists($filename)){
    return '';
  }
  $handle = @fopen($filename, "r");
  $i =0;
  if ($handle) {
    $ustring = '';

    while (!feof($handle)){
      $buffer = fgets($handle);
      $line = iconv("UTF-8","UTF-8//IGNORE//TRANSLIT",$buffer);  
      $ustring .= $line;
    }
    if (!feof($handle)) {
      echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
  }
  return $ustring;
}

function read_spml($type,$pid){
  $ustring=get_spml($type,$pid);
  $xml = simplexml_load_string($ustring);
  return $xml;
}

function pack_spml($type,$id){
//
  $dir = 'data/' . $type;
  if ($id) $dir .= '/' . $id;
  $filename = 'data/spml/' . $type . $id . '.spml';
  if (!is_dir($dir) || $type==''){
    return;
    die ('Does not exists ' . $type . '/' . $id);
  }
  if (!$handle = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    exit;
  } 

  /*******
   * Go through file ids for xml and png.
  **/
  $ids=array();
  $pngs=array();
  $cnt = 0;
  
  //scan id and copy PNGs if needed
//  foreach (glob($dir . '/*') as $Entry) {
  $d = dir($dir);
  while ($Entry = $d->Read()){
   if (!(($Entry == "..") || ($Entry == "."))){
      $pos=strrpos($Entry,".");
      $ext=strtolower(substr($Entry,$pos+1));
      $sid=substr($Entry,0,$pos);
      $cnt++;
      if ($ext=='xml'){
        if (strval(intval($sid))==$sid){
          $ids[]=$sid;
        }
      } else if ($ext=='png'){
        if (strval(intval($sid))==$sid){
          $pngs[]=$sid;
        }
      }
    }
  }
  sort($ids);
  
//old prefix spml
/*  $spml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";*/
//  $spml .= '<!DOCTYPE spml SYSTEM "http://www.signpuddle.net/spml_1.6.dtd">' . "\n";
//  $xroot = $xarr['root'];

//  $spml .= '<spml';
//  if ($xroot) {
//    $spml .= ' root="' . $xroot . '"';
//  }
//  $spml .= ' type="' . $type . '" puddle="' . $id . '" cdt="';
//  fwrite($handle, $spml);
//  fclose($handle);
//  //get file length
//  $size = filesize($filename);
//  if (!$handle = fopen($filename, 'a')) {
//    echo "Cannot open file ($filename)";
//    exit;
//  } 
//  $spml = time() . '" mdt="' . time() . '"';

  //Determin cdt and mdt values
//  $nextid = $xarr['nextid'];
//  if ($nextid) $spml .= ' nextid="' . $nextid . '"';
//  $spml .= '>' . "\n";

  //read in puddle spml
  $filespml = $dir . '.spml';
  $spml = file_get_contents($filespml);
  $spml = str_replace("</spml>","",$spml);

  //read in puddle xml
  $filexml = $dir . '.xml';
  $xml = simplexml_load_file($filexml);
//  $xarr = $xml->attributes();
  $items = $xml->children();
  foreach ($items as $item) $spml .= '  ' . $item->asXml() . "\n";
  $png = base64_encode(@file_get_contents($dir .  '.png'));
  if ($png) $spml .=  '  <png>' . $png . '</png>' . "\n";
  fwrite($handle, $spml);

  //now cycle through entires, then documents
  foreach ($ids as $eid){
    $spml = file_get_contents($dir . '/' . $eid . '.xml');
    if (in_array($eid,$pngs)) { 
      $png = base64_encode(file_get_contents($dir . '/' . $eid . '.png'));
      $spml = str_replace('</entry>','  <png>' . $png . '</png>' . "\n" . '</entry>',$spml);
    }
    fwrite($handle, $spml . "\n");
  }

  $spml = '</spml>';
  fwrite($handle, $spml);
  fclose($handle);

  //Determin cdt and mdt values
//  $string = file_get_contents($filename);
//  $string = iconv("UTF-8","UTF-8//IGNORE//TRANSLIT",$string);  
//  $xml = simplexml_load_string($string);

  $handle = @fopen($filename, "r");
  if ($handle) {
    $ustring = '';
    while (($buffer = fgets($handle, 4096)) !== false) {
      $line = iconv("UTF-8","UTF-8//IGNORE//TRANSLIT",$buffer);  
      $ustring .= $line;
    }
    if (!feof($handle)) {
      echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
  }

/* old code to update cdt & mdt
  $xml = simplexml_load_string($ustring);
  $cdt = '';
  $mdt = '';
  foreach($xml->children() as $entry) {
    $arr = $entry->attributes();
    if ($cdt){
      if ($cdt>$arr['cdt']) {
        $cdt = 0 + $arr['cdt'];
      }
    } else {
      $cdt = 0 + $arr['cdt'];
    }
    if ($mdt){
      if ($mdt<$arr['mdt']) {
        $mdt = 0 + $arr['mdt'];
      }
    } else {
      $mdt = 0 + $arr['mdt'];
    }
  }

  //now add text
  if (!$handle = fopen($filename, 'r+')) {
    echo "Cannot open file ($filename)";
    exit;
  } 
  fseek($handle,$size);
  fwrite($handle,$cdt);
  fseek($handle,$size+17);
  fwrite($handle,$mdt);
  fclose($handle);
*/
  return $filename;
}

function unpack_spml($type,$id){
  global $data;
  $filename = $data . '/spml/' . $type . $id . '.spml';//input file
  $dir = $data . '/' . $type;
  if (!is_dir($dir) || $type=='' || $id==''){
    mkdir ($dir);
  }

  $dir .= '/' . $id;
  if (!is_dir($dir) || $type=='' || $id==''){
    mkdir ($dir);
  }

  $handle = @fopen($filename, "r");
  if ($handle) {
    $ustring = '';
    while (($buffer = fgets($handle)) !== false) {
      $line = iconv("UTF-8","UTF-8//IGNORE//TRANSLIT",$buffer);  
      $ustring .= $line;
    }
    if (!feof($handle)) {
      echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
  }
  $xml = simplexml_load_string($ustring);

  $xarr = $xml->attributes();


  //prefix spml
  $spml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  $spml .= '<!DOCTYPE spml SYSTEM "http://www.signpuddle.net/spml_1.6.dtd">' . "\n";
  $xroot = $xarr['root'];

  $spml .= '<spml';
  if ($xroot) {
    $spml .= ' root="' . $xroot . '"';
  }
  $spml .= ' type="' . $type . '" puddle="' . $id . '"';

  //Determin cdt and mdt values
  $cdt = $xarr['cdt'];
  if ($cdt) $spml .= ' cdt="' . $cdt . '"';
  $mdt = $xarr['mdt'];
  if ($mdt) $spml .= ' mdt="' . $mdt . '"';

  $nextid = $xarr['nextid'];
  if ($nextid) $spml .= ' nextid="' . $nextid . '"';
  $spml .= '>' . "\n";
  $spml .= '</spml>' . "\n";
  $spmlfile = $data . '/' . $type . '/' . $id . '.spml';
  file_put_contents($spmlfile,$spml);
  
  //write sub XML file
  $spml = '<entry id="' . $id . '"'; //
  $cdt = $xarr['cdt'];
  if ($cdt) $spml .= ' cdt="' . $cdt . '"';
  $mdt = $xarr['mdt'];
  if ($mdt) $spml .= ' mdt="' . $mdt . '"';
  $spml .= '>' . "\n";
  
  //now add items...
  $items = $xml->children();
  $cnt=0;
  foreach ($items as $item) {
    $name = $item->getName();
    $itemXML = $item->asXml();
    switch($name){
      case "entry":
        $entry = $item;
        $e_id = $entry['id'];
        foreach($entry as $e_item) {
          $ei_name = $e_item->getName();
          if($ei_name == 'png' || $ei_name=='svg') {
            $outfile =  $data . '/' . $type . '/' . $id . '/' . $e_id . '.' . $ei_name;
            file_put_contents($outfile,base64_decode($e_item));
            $dom=dom_import_simplexml($e_item);
            $dom->parentNode->removeChild($dom);
          }
        }
        $outfile =  $data . '/' . $type . '/' . $id . '/' . $e_id . '.xml';
        $outxml = $entry->asXml();
        $outxml = str_replace("\n  \n","",$outxml);
//        $outxml = str_replace("\n\n","",$outxml);
        file_put_contents($outfile,$outxml."\n");
        break;
      case "png":
      case "svg":
        $outfile =  $data . '/' . $type . '/' . $id . '.' . $name;
        file_put_contents($outfile,base64_decode($item));
        break;
      default:
        $spml .= '  ' . $itemXML . "\n";
    }
  }
  $spml .= '</entry>' . "\n";
  $xmlfile = $data . '/' . $type . '/' . $id . '.xml';
  file_put_contents($xmlfile,$spml);
}
?>
