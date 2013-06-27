<?php
//development version of API
//semver 1.1.0-preview.1
include 'fsw.php';
include 'db.php';
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,
  PDO::FETCH_ASSOC);

$search = @$_REQUEST['search'];
$slang = @$_REQUEST['slang'];
$query = trim(@$_REQUEST['query']);
if (fswText($query)) $query = fsw2query($query);
$reverse = trim(@$_REQUEST['reverse']);
if (fswText($reverse)) $reverse = fsw2query($reverse);
$lang = @$_REQUEST['lang'];
$offset = intval(@$_REQUEST['offset']);
$mq = array();
if ($query) $mq[] = 'query=' . $query;
if ($lang) $mq[] = 'lang=' . $lang;
if ($search) $mq[] = 'search=' . $search;
if ($slang) $mq[] = 'slang=' . $slang;
if ($reverse) $mq[] = 'reverse=' . $reverse;
$mquery = implode('&',$mq);

$timein = microtime(true);
if ($reverse){

  $regexp = query2regex($reverse);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  try {
    $sel = 'SELECT SQL_CALC_FOUND_ROWS terms.* FROM terms, signs, links ';
    $sel .= 'where signs.id = links.sign_id and terms.id = links.term_id ';
    if ($search) {
      if(strpos($search, '_') === false && strpos($search, '%') === false) {
        $sel .= "and terms.text=:search";
      } else {
        $sel .= "and terms.text like :search";
      }
    }
    if ($slang) $sel .= ' and terms.lang=:slang';
    if ($lang) $sel .= ' and signs.lang=:lang';
  
    if ($regexp) {
      $cnt = count($regexp);
      $end = '';
      $part = '';
      $sel .=  " and signs.text REGEXP " . str_replace('/',"'",$regexp[0]);
      for ($i=1;$i<$cnt;$i++) {
        $part = ' and signs.id in (select id from signs where text REGEXP ' . str_replace('/',"'",$regexp[$i]) . 'END)';
        if ($end) {
          $end = str_replace('END)',$part . ')',$end);
        } else {
          $end = $part;
        }
      }
      $end = str_replace('END)',')',$end);
      $sel .= $end;
    }
    $sel .= ' order by text limit 10';
    if ($offset > 0 ) $sel .= ' offset ' . $offset;
    $stmt = $db->prepare($sel);
    if ($search) $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    if ($slang)  $stmt->bindParam(':slang', $slang);
    if ($lang)  $stmt->bindParam(':lang', $lang);
    $stmt->execute();
    $resp = array();
    $resp['meta'] = array();
    $resp['meta']['limit'] = 10;
    $resp['meta']['offset'] = $offset;
    $resp['meta']['totalResults'] = $db->query('SELECT FOUND_ROWS();')->fetch(PDO::FETCH_COLUMN);
    $resp['meta']['query'] = $mquery;
    $resp['meta']['searchTime'] = microtime(true)-$timein;
    $resp['results'] = $stmt->fetchAll();
    echo json_encode($resp);
  } catch (PDOException $e) {
    echo $e->getCode() . ' ' . $e->getMessage();
  }

} else {

  $regexp = query2regex($query);
  $rwhere = " where ";
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  try {
    $sel = 'SELECT SQL_CALC_FOUND_ROWS signs.* FROM signs';
    if ($search) {
      $sel .= ',terms,links where signs.id = links.sign_id and terms.id = links.term_id and ';
      if(strpos($search, '_') === false && strpos($search, '%') === false) {
        $sel .= "terms.text=:search";
      } else {
        $sel .= "terms.text like :search";
      }
      if ($slang) $sel .= ' and terms.lang=:slang';
      $rwhere = ' and ';
    }

    if ($lang) {
      $sel .= $rwhere . 'signs.lang=:lang';
      $rwhere = ' and ';
    }

    if ($regexp) {
      $cnt = count($regexp);
      $end = '';
      $part = '';
      $sel .=  $rwhere . "signs.text REGEXP " . str_replace('/',"'",$regexp[0]);
      for ($i=1;$i<$cnt;$i++) {
        $part = ' and signs.id in (select id from signs where text REGEXP ' . str_replace('/',"'",$regexp[$i]) . 'END)';
        if ($end) {
          $end = str_replace('END)',$part . ')',$end);
        } else {
          $end = $part;
        }
      }
      $end = str_replace('END)',')',$end);
      $sel .= $end;
    }
    $sel .= ' order by text limit 10';
    if ($offset > 0 ) $sel .= ' offset ' . $offset;
    $stmt = $db->prepare($sel);
    if ($search) {
      $stmt->bindParam(':search', $search, PDO::PARAM_STR);
      if ($slang) {
        $stmt->bindParam(':slang', $slang);
      }
    }
    if ($lang) {
      $stmt->bindParam(':lang', $lang);
    }
    $stmt->execute();
    $resp = array();
    $resp['meta'] = array();
    $resp['meta']['limit'] = 10;
    $resp['meta']['offset'] = $offset;
    $resp['meta']['totalResults'] = $db->query('SELECT FOUND_ROWS();')->fetch(PDO::FETCH_COLUMN);
    $resp['meta']['query'] = $mquery;
    $resp['meta']['searchTime'] = microtime(true)-$timein;
    $resp['results'] = $stmt->fetchAll();
    echo json_encode($resp);
  } catch (PDOException $e) {
    echo $e->getCode() . ' ' . $e->getMessage();
  }
}
?>
