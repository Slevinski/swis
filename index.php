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
<tr><td align=middle><font color="#117700" size="3" face="Arial, Helvetica, sans-serif"><strong><?php echo $edition;?></strong></font></td><td align=right><a href="http://semver.org"">semver</a> <?php echo $semver;?></td></tr>
</table>
  <hr>
</div>

<?php
}

/****************************
 * Config
 * fonts and more
 */

  $fonts = loadFonts();
  Switch($fmt){
  case 'json':
    $jsar['fonts'] = array();
    break;
  case 'div':
  default:
  //general page preamble
?>
<h2>SignWriting Icon Server</h2>
<b style="float:right"><?php echo $ed_date;?></b>
</br>
<table>
  <tr>
	<th class="per done section">Character Processing</th>
	<td>BSW, CSW, FSW, KSW, and query. The characters of draft-slevinski-signwriting-text and their associated processes are fully implemented.</td>
  </tr><tr>
	<th class="per done section">Image Support</th>
	<td>PNG and SVG images for symbols, signs, and text. Access the 16-bit symbol font using codes or names. Create logograms of sign images as a 2-dimensional arrangement of symbols. Combine and properly layout a sentence of logograms using multiple lanes.</td>
  </tr><tr>
	<th class="per beta section">Dictionaries / Documents</th>
	<td>The dictionaries are available from SignPuddle Online for a multitude of languages. These dictionaries are an account of the community of users. Dictionaries are easy to download and install.</td>
  </tr><tr>
	<th class="per alpha section"><a href="api.php">Public API</a></th>
	<td>Search the dictionaries and documents using the build in API. HTTP access is used for both the HTML pages and the JSON data.</td>
  </tr><tr>
	<th class="per rc section">Testing Suite</th>
	<td>PHPUnit testing suite with exhaustive range to regex testing</td>
  </tr><tr>
	<th class="per beta section">MediaWiki Support</th>
	<td>Plugin for MediaWiki to enable server for SignWriting images</td>
  </tr><tr>
	<th class="per section">Extended Fonts</th>
	<td>Additional handshapes and mouth movements to be determined</td>
  </tr>
</table>
<p>The SignWriting Icon Server is half of the client-server model of SignWriting Text.
Read about SignWriting Text in the Internet Draft published through
the Internet Engineering Task Force.  I-D name of <a href="http://tools.ietf.org/html/draft-slevinski-signwriting-text">draft-slevinski-signwriting-text</a>.

<?php
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
      $jsar['fonts'][$tfont] = $info;
      break;
    case 'div':
    default:
      echo '<tr>';
      echo '<th class="per ';
      if ($font == $item['font']) {
        echo 'rc ';
      }
      echo 'section">';
      echo '<a href="index.php' . req_attr('font',$item['font']) . '">' . $item['font'] . '</a>';
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
        $all .= '<a href="index.php' . req_attr('pdl',$tid['str']) . '#spml">' . $tid['str'] . '</a>';
        $all .= '</th>';
        $all .= '<td>Type: ' .  $tid['type'] . '<br>ID: ' . $tid['id'] . '<br>';
        $filename = $tid['str'] . '.spml';
        $all .= 'Size: ' . humanfilesize('data/spml/' . $filename);
        if ($pdl == $tid['str']) {
          $all .= '<br>Entries: ' . number_format($cnt_e) . '<br>';
          $all .= 'Sign count: ' . number_format($cnt_s) . '<br>';
          $all .= 'Sign Terms: ' . number_format($cnt_t);
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

Switch($fmt){
case 'json':
  break;
case 'div':
default:
  echo '<h2>Data Formats Available</h2>';
  echo '<br>';
  echo '<table>';
  echo '<tr>';
  echo '<th class="per section"><a href="index.php' . req_attr('fmt','json') . '">json</a></th>';
  echo '<td>json output for use with JavaScript</td>';
  echo '</tr>';
  echo '<tr>';
  echo '<th class="per section"><a href="index.php' . req_attr('fmt','div') . '">div</a></th>';
  echo '<td>Inner html for use in a div</td>';
  echo '</tr>';
  echo '<tr>';
  echo '<th class="per section"><a href="index.php?' . req_attr('fmt','html') . '">html</a></th>';
  echo '<td>Default api format</td>';
  echo '</tr>';
  echo '</table>';
?>
<h2>16-bit Symbol Font</h2><br>
<p>Available through the <b>glyph.php</b> script.
<h3>Identity Attributes</h3><br>
<table>
  <tr>
	<th class="per section">key</th>
	<td>use symbol key, with or without <b>S</b> prefix</td>
	<td><img src="glyph.php?key=10000"> or <img src="glyph.php?key=S10100"></td>
  </tr><tr>
	<th class="per section">code</th>
	<td>use the symbol code as a 16-bit number</td>
	<td><img src="glyph.php?code=1"> or <img src="glyph.php?code=97"></td>
  </tr><tr>
	<th class="per section">bsw</th>
	<td>use the bsw string for symbols</td>
	<td><img src="glyph.php?bsw=130110120"> or <img src="glyph.php?bsw=131110120"></td>
  </tr><tr>
	<th class="per section">sym</th>
	<td>Use the Private Use Area Unicode characters</td>
	<td><img src="glyph.php?sym=󽠰󽠐󽠠"> or <img src="glyph.php?sym=󽠱󽠐󽠠"></td>
  </tr>
</table>

<h3>Style Attributes</h3><br>
<table>
  <tr>
	<th class="per section">font</th>
	<td>Use <b>svg</b> or <b>png</b> values.  Add additional font numbers if available.</td>
	<td><img src="glyph.php?key=10000&font=png"> or <img src="glyph.php?key=10100&font=svg"></td>
  </tr><tr>
	<th class="per section">size</th>
	<td>1 is the standard size.  Use decimal value in a limited range, from .5 to 7 or more.</td>
	<td><img src="glyph.php?key=10000&size=3"> or <img src="glyph.php?key=10000&size=.7"></td>
  </tr><tr>
	<th class="per section">line</th>
	<td>specify the line color</td>
	<td><img src="glyph.php?key=10000&line=00ff00"> or <img src="glyph.php?key=10100&line=ff00ff"></td>
  </tr><tr>
	<th class="per section">fill</th>
	<td>specify the fill color of the palms and arrow heads</td>
	<td><img src="glyph.php?key=10000&fill=00ff00"> or <img src="glyph.php?key=10100&fill=ff00ff"></td>
  </tr><tr>
	<th class="per section">colorize</th>
	<td>colorize the line according to the standard colors.</td>
	<td><img src="glyph.php?key=10000&colorize=1"> or <img src="glyph.php?key=20500&colorize=1"></td>
  </tr><tr>
	<th class="per section">name</th>
	<td>give the image a file name.</td>
	<td><img src="glyph.php?key=10000&name=symbol-name"> or <img src="glyph.php?key=10100&name=other_name"></td>
  </tr>
</table>

<h2>Logographic Sign Images</h2><br>
<p>Available through the <b>glyphogram.php</b> script.

<h3>Identity Attributes</h3><br>
<table>
  <tr>
	<th class="per section">ksw</th>
	<td>use the ksw string for the sign</td>
	<td><img src="glyphogram.php?ksw=M18x29S14c20n19xn29S271063xn11"> or <img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32"></td>
  </tr><tr>
	<th class="per section">text</th>
	<td>use the ksw or fsw sign strings</td>
	<td><img src="glyphogram.php?text=M18x29S14c20n19xn29S271063xn11"> or <img src="glyphogram.php?text=M518x533S1870a489x515S18701482x490S20500508x496S2e734500x468"></td>
  </tr><tr>
	<th class="per section">horizontal panel</th>
	<td>Use the variant display of <a href="http://signpuddle.net/wiki/index.php/MSW:Variant_Display_Form#10.D._Panel">KSW panel form</a> to properly layout an entire column or row.</td>
	<td><img src="glyphogram.php?panel=D200x150_B39x75S14c20n19xn29S271063xn11_B115x75S1870an11x15S18701n18xn10S205008xn4S2e7340xn32_B157x75S38802n4xn36"></td>
  </tr>
	<th class="per section">vertical panel</th>
	<td>Use the variant display of <a href="http://signpuddle.net/wiki/index.php/MSW:Variant_Display_Form#10.D._Panel">KSW panel form</a> to properly layout an entire column or row.</td>
	<td><img src="glyphogram.php?panel=D230x250_M115x49S14c20n19xn29S271063xn11_M115x150S1870an11x15S18701n18xn10S205008xn4S2e7340xn32_B115x207S38800n36xn4"></td>
  </tr>
</table>

<h3>Style Attributes</h3><br>
<table>
  <tr>
	<th class="per section">font</th>
	<td>Use <b>svg</b> or <b>png</b> values.  Add additional font numbers if available.</td>
	<td><img src="glyphogram.php?ksw=M18x29S14c20n19xn29S271063xn11&font=png"> or <img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=svg"></td>
  </tr><tr>
	<th class="per section">size</th>
	<td>1 is the standard size.  Use decimal value in a limited range, from .5 to 7 or more.</td>
	<td><img src="glyphogram.php?ksw=M18x29S14c20n19xn29S271063xn11&font=png&size=.7"> or <img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=svg&size=1.7"></td>
  </tr><tr>
	<th class="per section">line</th>
	<td>specify the line color</td>
	<td><img src="glyphogram.php?ksw=M18x29S14c20n19xn29S271063xn11&font=png&line=00ff00"> or <img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=svg&line=ff00ff"></td>
  </tr><tr>
	<th class="per section">fill</th>
	<td>specify the fill color of the palms and arrow heads</td>
	<td><img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=png&fill=00ff00"> or <img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=svg&fill=ff00ff"></td>
  </tr><tr>
	<th class="per section">back</th>
	<td>specify the background color logogram</td>
	<td><img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=png&back=00ff00"> or <img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=svg&back=ff00ff"></td>
  </tr><tr>
	<th class="per section">pad</th>
	<td>specify the padding around the logogram</td>
	<td><img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=png&back=00ff00&pad=10"> or <img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=svg&back=ff00ff&pad=10"></td>
  </tr><tr>
	<th class="per section">no bound</th>
	<td>specify a tight bounding box around the logogram based</td>
	<td>
	<img src="glyphogram.php?ksw=M53x70S14c2016x12S2710638x30S2ff00n18xn17&font=png&back=00ff00&bound="> 
	</td>
  </tr><tr>
	<th class="per section">bound=c</th>
	<td>specify the bounding box around the logogram based on its center</td>
	<td>
	<img src="glyphogram.php?ksw=M53x70S14c2016x12S2710638x30S2ff00n18xn17&font=png&back=00ff00&bound=c">
	</td>
  </tr><tr>
	<th class="per section">bound=v</th>
	<td>specify the bounding box around the logogram based on its vertical center</td>
	<td>
	<img src="glyphogram.php?ksw=M53x70S14c2016x12S2710638x30S2ff00n18xn17&font=png&back=00ff00&bound=v">
	</td>
  </tr><tr>
	<th class="per section">bound=h</th>
	<td>specify the bounding box around the logogram based on its horizontal center</td>
	<td>
	<img src="glyphogram.php?ksw=M53x70S14c2016x12S2710638x30S2ff00n18xn17&font=png&back=00ff00&bound=h"> 
	</td>
  </tr><tr>
	<th class="per section">colorize</th>
	<td>colorize the line according to the standard colors.</td>
	<td><img src="glyphogram.php?ksw=M18x29S14c20n19xn29S271063xn11&font=png&colorize=1"> or <img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=svg&colorize=1"></td>
  </tr><tr>
	<th class="per section">name</th>
	<td>give the image a file name.</td>
	<td><img src="glyphogram.php?ksw=M18x29S14c20n19xn29S271063xn11&font=png&name=hello"> or <img src="glyphogram.php?ksw=M18x33S1870an11x15S18701n18xn10S205008xn4S2e7340xn32&font=svg&name=world"></td>
  </tr>
</table>
<?php  
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
