<?php
include 'msw.php';
include 'spml.php';
$fmt = @$_REQUEST['fmt'];
$cmd = @$_REQUEST['cmd'];
$ui = @$_REQUEST['ui'];
$pdl = @$_REQUEST['pdl'];
$font = @$_REQUEST['font'];
Switch($fmt){
case 'json':
  break;
case 'div':
  break;
default:
  $fmt = 'html';
}

function req_attr ($set, $value){
  $attr = array('fmt','cmd','ui','font','pdl');
  foreach ($attr as $i=>$at){
    if ($at != $set){
      global $$at;
    }
  }
  $$set = $value;
  $return = '';
  foreach ($attr as $i=>$at){
    if ($$at) {
      if ($return) {
        $return .= '&';
      } else {
        $return .= '?';
      }
      $return .= $at . '=' . $$at;
    }
  }
  return $return;
}

if (!$cmd){ $cmd='config';}

/****************************
 * General format setups
 */
Switch($fmt){
case 'json':
  $jsar = array();
  break;
case 'div':
  break;
default:
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="index.css" rel="stylesheet" type="text/css" media="all">
<script src="msw.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
</script>

<title>SignWriting Icon Server</title></head>
<body><div id="command" class="command"><center><img src="media/logo.png" alt="Open SignPuddle logo" border=0><br><br>
<form method="post" action="README"><button class="cmd" type="submit">README</button></form>
<form method="post" action="http://www.signpuddle.net"><button class="cmd" type="submit">SignPuddle Report</button></form>
<form method="post" action="http://www.signpuddle.com"><button class="cmd" type="submit">SignPuddle Status</button></form>
<form method="post" action="http://www.signwriting.org/lessons/"><button class="cmd" type="submit">SignWriting Lessons</button></form>
<form method="post" action="http://www.signwriting.org/forums/swlist/"><button class="cmd" type="submit">SignWriting List</button></form>
<form method="post" action="http://www.signpuddle.org"><button class="cmd" type="submit">SignPuddle Online</button></form>
<form method="post" action="http://www.signbank.org/signpuddle/help"><button class="cmd" type="submit">SignPuddle Help</button></form>
<form method="" action="http://www.signwriting.org/lessons/iswa/"><button class="cmd" type="submit">ISWA Lessons</button></form>
<form method="" action="http://signbank.org/iswa"><button class="cmd" type="submit">ISWA Reference</button></form>
<form method="" action="http://signpuddle.net/iswa"><button class="cmd" type="submit">ISWA Font</button></form>
<form method="" action="http://www.movementwriting.org/symbolbank/index.html#ISWA2010"><button class="cmd" type="submit">ISWA SymbolBank</button></form>
<form method="" action="http://github.com/Slevinski/swis"><button class="cmd" type="submit">SignWriting Icon Server</button></form>
<form method="" action="http://github.com/Slevinski/swic"><button class="cmd" type="submit">SignWriting Image Client</button></form>
<form method="" action="http://signpuddle.net/wiki/index.php/MSW"><button class="cmd" type="submit">Modern SignWriting WIki</button></form>
</center></div>

<div class="detail">
  <div id="header" class="header">
  <table width="90%" border=0><tr><td align=left><font face="Arial, Helvetica, sans-serif"><a href="http://signpuddle.com">SignPuddle Standard</a></font></td><td rowspan=2 align=right><a href="http://www.gnu.org/copyleft/gpl.html"><img border=0 src="media/gplv3.png"></a></td></tr><tr><td align=middle valign=top>
  <font color="#117700" size="6" face="Arial, Helvetica, sans-serif"><strong>SignWriting Icon Server</strong></font></td></tr>
<tr><td align=middle><font color="#117700" size="3" face="Arial, Helvetica, sans-serif"><strong>API</strong></font></td><td align=right><a href="http://semver.org"">semver</a> <?php echo $semver;?></td></tr>
</table>
  <hr>
</div>

<?php
  echo '<h2>Data Formats available</h2>';
  echo '<b style="float:right">' . $ed_date . '</b>';
  echo '<br>';
  echo '<table>';
  echo '<tr>';
  echo '<th class="per section"><a href="api.php' . req_attr('fmt','json') . '">json</a></th>';
  echo '<td>json output for use with JavaScript</td>';
  echo '</tr>';
  echo '<tr>';
  echo '<th class="per section"><a href="api.php' . req_attr('fmt','div') . '">div</a></th>';
  echo '<td>Inner html for use in a div</td>';
  echo '</tr>';
  echo '<tr>';
  echo '<th class="per section"><a href="api.php?' . req_attr('fmt','html') . '">html</a></th>';
  echo '<td>Default api format</td>';
  echo '</tr>';
  echo '</table>';
}


/****************************
 * Config section
 */
if ($cmd=='config'){
  $api_name = 'SignPuddle Server API';
  $api_semver = $semver;
  $api_fmt = array('json','div','html');
  $api_cmd = array('config');
  Switch($fmt){
  case 'json':
    $jsar['api'] = array();
    $jsar['api']['name'] = $api_name;
    $jsar['api']['semver'] = $api_semver;
    $jsar['api']['fmt'] = $api_fmt;
    $jsar['api']['cmd'] = $api_cmd;
    
    break;
  case 'div':
  default:
    echo '<h2>API Configuration</h2>';
    echo '<br>';
    echo '<table>';
    echo '<tr>';
    echo '<th class="per section">name</th>';
    echo '<td>' . $api_name . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th class="per section">semver</th>';
    echo '<td>' . $api_semver . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th class="per section">fmt</th>';
    echo '<td>' . implode(', ',$api_fmt) . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th class="per section">cmd</th>';
    echo '<td>' . implode(', ',$api_cmd) . '</td>';
    echo '</tr>';
    echo '</table>';
  }

/****************************
 * Config
 * fonts
 */

  $fonts = loadFonts();
  Switch($fmt){
  case 'json':
    $jsar['config'] = array();
    $jsar['config']['fonts'] = array();
    break;
  case 'div':
  default:
    echo '<h2>Fonts Available</h2>';
    echo '<br>';
    echo '<table>';
  }

  foreach ($fonts as $item){
    Switch($fmt){
    case 'json':
      $tfont = $item['font'];
      $info = array();
      $info['name'] = $item['name'];
      $info['author'] = $item['author'];
      $info['license'] = $item['license'];
      $jsar['config']['fonts'][$tfont] = $info;
      break;
    case 'div':
    default:
      echo '<tr>';
      echo '<th class="per ';
      if ($font == $item['font']) {
        echo 'rc ';
      }
      echo 'section">';
      echo '<a href="api.php' . req_attr('font',$item['font']) . '">' . $item['font'] . '</a>';
      echo '</th>';
      echo '<td>Reserved font name: ' .  $item['name'] . '<br>' . $item['author'] . '<br>' . $item['license'] . '</td>';
      echo '</tr>';
    }
  }

  Switch($fmt){
  case 'json':
    if ($font) $jsar['font'] = $font;
    break;
  case 'div':
  default:
    echo '</table>';
  }


/****************************
 * Config
 * signpuddle collections available
 */

  if ($pdl){
    $tid = typeid($pdl);
    $spml = get_spml($tid['type'],$tid['id']);

    $pattern = '/\<entry/';
    $cnt_e = preg_match_all($pattern, $spml, $matches);

    $pattern = query2regex('Q');
    $cnt_s = preg_match_all($pattern[0], $spml, $matches);

    $pattern = query2regex('QT');
    $cnt_t = preg_match_all($pattern[0], $spml, $matches);
  }

  $files = glob('data/spml/*.spml');
  Switch($fmt){
  case 'json':
    $jsar['spml'] = array();
    $jsar['other'] = array();
    if ($pdl){
      $info = array();
      $info['str'] = $pdl;
      $info['entries'] = $cnt_e;
      $info['signs'] = $cnt_s;
      $info['terms'] = $cnt_t;
      $jsar['pdl'] = $info;
    }
    break;
  case 'div':
  default:
    $all = '';
    $other = '';
  }

  foreach ($files as $item){
    $item = basename($item,'.spml');
    $tid = typeid($item);
        
    Switch($fmt){
    case 'json':
      if ($tid['valid']){ 
        $jsar['spml'][$tid['str']] = $tid;
      } else {
        $jsar['other'][$tid['str']] = $tid;
      }
      break;
    case 'div':
    default:
      if ($tid['valid']){
        $all .= '<tr>';
        $all .= '<th class="per ';
        if ($pdl == $tid['str']) {
          $all .= 'rc ';
        }
        $all .= 'section">';
        $all .= '<a href="api.php' . req_attr('pdl',$tid['str']) . '#spml">' . $tid['str'] . '</a>';
        $all .= '</th>';
        $all .= '<td>Type: ' .  $tid['type'] . '<br>ID: ' . $tid['id'] . '<br>';
        $filename = $tid['str'] . '.spml';
        $all .= 'Size: ' . humanfilesize('data/spml/' . $filename);
        if ($pdl == $tid['str']) {
          $all .= '<br>Entries: ' . number_format($cnt_e) . '<br>';
          $all .= 'Sign count: ' . number_format($cnt_s) . '<br>';
          $all .= 'Terms: ' . number_format($cnt_t);
        }

        $all .= '</td></tr>';
      } else {
        $other .= '<tr>';
        $other .= '<th class="per section">' . $tid['str'] . '</th>';
        $filename = $tid['str'] . '.spml';
        $other .= '<td>Name: ' .  $filename . '<br>';
        $other .= 'Size: ' . humanfilesize('data/spml/' . $filename) . '</td>';
        $other .= '</tr>';
      }
    }
  }

  Switch($fmt){
  case 'json':
    break;
  case 'div':
  default:
    if ($all){
      echo '<h2 id="spml">SPML Available</h2><br><table>';
      echo $all;
      echo '</table>';
    }
    if ($other){
      echo '<h2>SPML Other</h2><br><table>';
      echo $other;
      echo '</table>';
    }
  }



}



/****************************
 * The end section and output
 */
 
Switch($fmt){
case 'json':
  echo json_encode($jsar);
  break;
case 'div':
  break;
default:
?>


</div>
<br clear="all"><br>
<hr>
<div id="footer" class="footer"><table cellpadding="5" width="95%"><tr><td valign="top"><a href="http://scripts.sil.org/OFL"><img src="media/ofl.png"></a></td><td align=middle>
<b>SignPuddle Standard: SignWriting Icon Server</b><br><b>Copyright 2007-2012 Stephen E Slevinski Jr. Some Rights Reserved.</b><br>Except where otherwise noted, this work is licensed under<br><a href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution ShareAlike 3.0</td><td valign="top" align="right"><a href="http://creativecommons.org/licenses/by-sa/3.0/"><img src="media/by-sa.png"></a>
</td></tr></table></div><hr><br>
</body>
</html>﻿﻿﻿﻿﻿﻿
<?php
}
?>
