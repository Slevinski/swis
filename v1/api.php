<?php
//development version of API
//semver 1.1.0-preview.2
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
  if ($reverse=="Q") $reverse='';
  $regexp = query2regex($reverse);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  try {
    $and = ' where ';
    $sel = 'SELECT SQL_CALC_FOUND_ROWS t_out.* from terms t_out';

    if ($regexp or $lang) {
      $sel .= ', terms t_in where t_in.entry_id=t_out.entry_id';
      $and = ' and ';
      if ($lang) {
        $sel .= ' and t_in.lang=:lang';
      } else {
        $sel .= ' and t_in.lang in(select lang from languages where signed=1)';
      }

      if ($regexp) {
        $cnt = count($regexp);
        $end = '';
        $part = '';
        $sel .=  " and t_in.text REGEXP " . str_replace('/',"'",$regexp[0]);
        for ($i=1;$i<$cnt;$i++) {
          $part = ' and t_in.id in (select id from terms where text REGEXP ' . str_replace('/',"'",$regexp[$i]);
          if ($lang) {
            $part .= ' and lang=:lang';
          } else {
            $part .= ' and lang in(select lang from languages where signed=1)';
          }
          $part .= 'END)';
          if ($end) {
            $end = str_replace('END)',$part . ')',$end);
          } else {
            $end = $part;
          }
        }
        $end = str_replace('END)',')',$end);
        $sel .= $end;
      }

    }

    if ($slang) {
      $sel .= $and . 't_out.lang=:slang';
    } else {
      $sel .= $and . 't_out.lang in(select lang from languages where signed=0)';
    }

    if ($search) {
      if(strpos($search, '_') === false && strpos($search, '%') === false) {
        $sel .= " and t_out.text=:search";
      } else {
        $sel .= " and t_out.text like :search";
      }
    }

    $sel .= ' order by t_out.text limit 10';
    if ($offset > 0 ) $sel .= ' offset ' . $offset;
    $stmt = $db->prepare($sel);
    if ($search) {
      $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    }
    if ($lang) {
      $stmt->bindParam(':lang', $lang);
    }
    if ($slang) {
      $stmt->bindParam(':slang', $slang);
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
} else {
  $regexp = query2regex($query);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  try {
    $and = ' where ';
    $sel = 'SELECT SQL_CALC_FOUND_ROWS t_out.* from terms t_out';

    if ($search or $slang) {
      $sel .= ', terms t_in where t_in.entry_id=t_out.entry_id';
      $and = ' and ';
      if ($slang) {
        $sel .= ' and t_in.lang=:slang';
      } else {
        $sel .= ' and t_in.lang in(select lang from languages where signed=0)';
      }

      if ($search) {
        if(strpos($search, '_') === false && strpos($search, '%') === false) {
          $sel .= " and t_in.text=:search";
        } else {
          $sel .= " and t_in.text like :search";
        }
      }
    }

    if ($lang) {
      $sel .= $and . 't_out.lang=:lang';
    } else {
      $sel .= $and . 't_out.lang in(select lang from languages where signed=1)';
    }


    if ($regexp) {
      $cnt = count($regexp);
      $end = '';
      $part = '';
      $sel .=  " and t_out.text REGEXP " . str_replace('/',"'",$regexp[0]);
      for ($i=1;$i<$cnt;$i++) {
        $part = ' and t_out.id in (select id from terms where text REGEXP ' . str_replace('/',"'",$regexp[$i]);
        if ($lang) {
          $part .= ' and lang=:lang';
        } else {
          $part .= ' and lang in(select lang from languages where signed=1)';
        }
        $part .= 'END)';
        if ($end) {
          $end = str_replace('END)',$part . ')',$end);
        } else {
          $end = $part;
        }
      }
      $end = str_replace('END)',')',$end);
      $sel .= $end;
    }
    $sel .= ' order by t_out.text limit 10';
    if ($offset > 0 ) $sel .= ' offset ' . $offset;
    $stmt = $db->prepare($sel);
    if ($search) {
      $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    }
    if ($lang) {
      $stmt->bindParam(':lang', $lang);
    }
    if ($slang) {
      $stmt->bindParam(':slang', $slang);
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
