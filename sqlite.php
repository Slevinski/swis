<?php
/**
 * SQLite Creation Script
 * 
 * This file is part of SWIS: the SignWriting Image Server.
 * 
 * SWIS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * SWIS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with SWIS.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * END Copyright
 *  
 * @copyright 2007-2012 Stephen E Slevinski Jr 
 * @author Steve Slevinski (slevin@signpuddle.net)  
 * @version 3
 * @section License 
 *   GPL 3, http://www.opensource.org/licenses/gpl-3.0.html
 * @brief SQLite Creation Script
 * @file
 *   
 */
 
include "bsw.php";
include "spl.php";
include "image.php";
include 'iswa/data/iswa.i18n.php';
set_time_limit(0);
ini_set("memory_limit","108M");

$lang = @$_REQUEST['lang'];
$font = @$_REQUEST['font'];
$main = @$_REQUEST['main'];

$fonts = array();
$fonts['png1']=array('ISWA 2010 PNG Standard','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['png2']=array('ISWA 2010 PNG Inverse','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['png3']=array('ISWA 2010 PNG Shadow','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['png4']=array('ISWA 2010 PNG Colorized','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['svg1']=array('ISWA 2010 SVG Refinement','Symbols designed by Valerie Sutton, refinement by Adam Frost, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['svg2']=array('ISWA 2010 SVG Line Trace','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['svg3']=array('ISWA 2010 SVG Shadow Trace','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['svg4']=array('ISWA 2010 SVG Smooth','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['svg5']=array('ISWA 2010 SVG Angular','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['pbm1']=array('ISWA 2010 PBM Line','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['pbm2']=array('ISWA 2010 PBM Fill','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['pbm3']=array('ISWA 2010 PBM Shadow','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');
$fonts['txt1']=array('ISWA 2010 ASCII','Symbols designed by Valerie Sutton, refactored by Stephen E Slevinski Jr.','SIL Open Font License, Version 1.1');

if ($lang){

//////////////////////////
  echo "<h1>Lang $lang</h1>";
  $msg = $messages[$lang];

  $output = <<<EOT
-- SQLite ISWA 2010 language $lang database 
-- version 2.0
-- author Stephen E Slevinski Jr
-- http://www.signpuddle.net

--
-- Database: `iswa`
--

-- --------------------------------------------------------

EOT;

  /**
   * ISWA Name Table def
   */

  $output .= <<<EOT
CREATE TABLE IF NOT EXISTS iswa_name (
  lang TEXT,
  key TEXT,
  name TEXT
);

EOT;

  /**
   * write iswa name data
   */
  $outhead = <<<EOT
INSERT INTO iswa_name VALUES
EOT;
  $outrows='';
  foreach ($msg as $key=>$val){
    $key = str_replace("iswa_" , '' , $key);
    if ($outrows) { $outrows .= ",\n";}
    $outrows = "(";
    $outrows .= "'" . $lang . "', ";
    $outrows .= "'" . $key . "', ";
    $outrows .= "'" . $val . "');";
    $output .= $outhead . $outrows . "\n";
  }
  $output .= "\n\n";

  //write file
  $fp = fopen('iswa_lang_' . $lang . '.sql', 'w');
  fwrite($fp, $output);
  fclose($fp);



} else if ($font) {

//////////////////////////
  echo "<h1>Font $font</h1>";

  $name = $fonts[$font][0];
  $author = $fonts[$font][1];
  $license = $fonts[$font][2];

  $pre = substr($font,0,3);
  switch ($pre){
    case "svg":
    case "txt":
      $storage = "TEXT";
      break;
    default:
      $storage = "BLOB";
  }
  $output = <<<EOT
-- SQLite ISWA 2010 font $name ($font) database 
-- version 2.0
-- author Stephen E Slevinski Jr
-- http://www.signpuddle.net

--
-- Database: `iswa`
--

-- --------------------------------------------------------

EOT;

  /**
   * Symbol Fonts tables def
   */

  $output .= <<<EOT
CREATE TABLE IF NOT EXISTS symfont (
  font TEXT,
  name TEXT,
  author TEXT,
  license TEXT
);

INSERT INTO symfont VALUES ('$font', '$name', '$author', '$license');

CREATE TABLE font_$font (
  code INTEGER PRIMARY KEY,
  glyph $storage
);

EOT;


  /**
   * write font glyph data
   */
  $outhead = <<<EOT
INSERT INTO font_$font VALUES
EOT;
  $outrows='';


  for ($b=0;$b<652;$b++){
    $base = dechex($b+256);
    for ($f=0;$f<6;$f++){
      for ($r=0;$r<16;$r++){
        $key = dechex($b+256) . dechex($f) . dechex($r);
        if (validKey($key)){
          $code = (hexdec($base)-256)*96 + $f*16 + $r + 1;
          $file = 'iswa/' . $font . '/' . $base . '/' . $key . '.' . $pre;
          $imagestr = file_get_contents($file);
          if ($outrows) { $outrows .= ",\n";}
          $outrows = "(";
          $outrows .= $code . ", ";
          if ($storage=="BLOB"){
            $outrows .= "X'" . bin2hex($imagestr) . "');\n";
          } else {
            if ($pre == 'svg') $imagestr = glyph_svg_trim($imagestr);
            $outrows .= "'" . str_replace("'","''",$imagestr) . "');\n";
          }
          $output .= $outhead . $outrows . "\n";
        }
        $output .= "\n\n";
      }
    }
  }

  //write file
  $fp = fopen('iswa_font_' . $font . '.sql', 'w');
  fwrite($fp, $output);
  fclose($fp);


} else if ($main) {

//////////////////////////
  echo "<h1>Main</h1>";
  $SymbolGroups = loadSymbolGroups();
  $BaseSymbols = loadBaseSymbols();
  /**
   * write header
   */
  $output = <<<EOT
-- SQLite ISWA 2010 main database 
-- version 2.0
-- author Stephen E Slevinski Jr
-- http://www.signpuddle.net

--
-- Database: `iswa`
--

-- --------------------------------------------------------

EOT;

  /**
   * SymbolGroup Table def
   */

  $output .= <<<EOT
CREATE TABLE symbolgroup (
  code INTEGER PRIMARY KEY,
  num INTEGER,
  cat_num INTEGER,
  grp_num INTEGER,
  color TEXT
);

EOT;

  /**
   * write symbol group data
   */
  $outhead = <<<EOT
INSERT INTO symbolgroup VALUES
EOT;
  $outrows='';
  foreach ($SymbolGroups as $group=>$sg){
    if ($outrows) { $outrows .= ",\n";}
    $outrows = "(";
    $outrows .= $sg['code'] . ", ";
    $outrows .= $sg['num'] . ", ";
    $outrows .= $sg['cat_num'] . ", ";
    $outrows .= $sg['grp_num'] . ", ";
    $outrows .= "'" . $sg['color'] . "');";
    $output .= $outhead . $outrows . "\n";
  }
  $output .= "\n\n";

  /**
   * BaseSymbol Table def
   */

  $output .= <<<EOT
CREATE TABLE basesymbol (
  code INTEGER PRIMARY KEY,
  sg_code INTEGER,
  num INTEGER,
  bas_num INTEGER,
  var_num INTEGER,
  vars INTEGER,
  fills INTEGER,
  rots INTEGER
);

EOT;

  /**
   * write symbol group data
   */
  $outhead = <<<EOT
INSERT INTO basesymbol VALUES
EOT;

  foreach ($BaseSymbols as $base=>$bs){
    if ($outrows) { $outrows .= ",\n";}
    $outrows = "(";
    $outrows .= $bs['code'] . ", ";
    $outrows .= $bs['sg_code'] . ", ";
    $outrows .= $bs['num'] . ", ";
    $outrows .= $bs['bas_num'] . ", ";
    $outrows .= $bs['var_num'] . ", ";
    $outrows .= $bs['vars'] . ", ";
    $outrows .= $bs['fills'] . ", ";
    $outrows .= $bs['rots'] . ");";
    $output .= $outhead . $outrows . "\n";
  }
  $output .= "\n\n";

  /**
   * Symbol Table def
   */

  $output .= <<<EOT
CREATE TABLE symbol (
  code INTEGER PRIMARY KEY,
  fill INTEGER,
  rot INTEGER,
  sg_code INTEGER,
  bs_code INTEGER,
  w INTEGER,
  h INTEGER
);

EOT;

  /**
   * write symbol group data
   */
  $output .= <<<EOT
--
-- load data for table `symbol`
--

EOT;

  $outhead = <<<EOT
INSERT INTO symbol VALUES

EOT;

  $outrows = "";

  //should start at 0
  for ($b=0;$b<652;$b++){
    $base = dechex($b+256);
    for ($f=0;$f<6;$f++){
      for ($r=0;$r<16;$r++){
        $key = dechex($b+256) . dechex($f) . dechex($r);
        if (validKey($key)){
          $bs = $BaseSymbols[$b+256];
          $group = $bs['sg_code'];
          $code = (hexdec($base)-256)*96 + $f*16 + $r + 1;
  //        $file = 'iswa/' . $style. '/' . $base . '/' . $key . '.png';
          $file = 'iswa/png1/' . $base . '/' . $key . '.png';
  
          // image stuff
          $imagehex = "";
          $iw = 0;
          $ih = 0;
          $imagestr = file_get_contents($file);
          $image= imagecreatefromstring($imagestr); 
          $iw= ImageSX($image);
          $ih= ImageSY($image);
          imagedestroy($image);

        //  if ($outrows) { $outrows .= ",\n";}
          $outrows = "(";
          $outrows .= $code . ", ";
          $outrows .= (1+$f) . ", ";
          $outrows .= (1+$r) . ", ";
          $outrows .= $group . ", ";
          $outrows .= hexdec($base) . ", ";
          $outrows .= $iw . ", ";
          $outrows .= $ih . ");\n";
  //        $outrows .= "X'" . bin2hex($imagestr) . "');\n";
          $output .= $outhead . $outrows;
        }
      }
    }
  }

  //write file
  $fp = fopen('iswa_main.sql', 'w');
  fwrite($fp, $output);
  fclose($fp);

} else {

//////////////////////////
// Selections
  echo "<h1>Main</h1>";
  echo "<a href='sqlite.php?main=main'>Main Database Script</a>";

  echo "<h1>Fonts</h1>";
  foreach (glob("iswa/*",GLOB_ONLYDIR) as $dir){
    $dir = str_replace("iswa/","",$dir);
    if ($dir <> "data"){
      echo "<a href='sqlite.php?font=$dir'>Font $dir</a><br>";
    }
  }

  $langs = array_keys($messages);
  echo "<h1>Languages</h1>";
  foreach ($langs as $lang){
    echo "<a href='sqlite.php?lang=$lang'>Language $lang</a><br>";
  }
 
}

?>
