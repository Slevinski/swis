<?php
/**
 * SWIS Image
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
 * @brief base image function library
 * @file
 *   
 */

function glyph_png($key,$ver,$size,$line,$fill,$back, $colorize){
  if (!$ver) $ver=1;
  $base = substr($key,0,3);
  $file = 'iswa/png' . $ver . '/' . $base . '/' . $key . '.png';
  $im_src = imagecreatefrompng($file);
  if ($colorize) $line='';
//   if (!$colorize and !$line){$line = "000000";}
  if ($line){
    list($r,$g,$b) = array_values(str_split($line,2));
    $r=hexdec($r);
    $g=hexdec($g);
    $b=hexdec($b);
    //2 for fill, 1 for line
    if (imagecolorstotal( $im_src)==1){
      $index=0;
    } else {
      $index=1;
    }
     imageColorSet($im_src,$index,$r,$g,$b);
  }

  if ($fill){
    list($r,$g,$b) = array_values(str_split($fill,2));
    $r=hexdec($r);
    $g=hexdec($g);
    $b=hexdec($b);
    //2 for fill, 1 for line
    if (imagecolorstotal( $im_src)==2){
      $index=0;
    } else {
      $index=2;
    }
    imageColorSet($im_src,$index,$r,$g,$b);
  }

  if ($size){
    $width = imagesx($im_src);
    $height = imagesy($im_src);
    $w = ceil($width*$size);
    $h = ceil($height*$size);
    $im = imagecreatetruecolor($w, $h);
 
    /* making the new image transparent */
    $background = imagecolorallocate($im, 254, 0, 0);
   ImageColorTransparent($im, $background); // make the new temp image all transparent
   imagealphablending($im, false); // turn off the alpha blending to keep the alpha channel
   imagesavealpha ($im, true );
   imagecopyresampled($im, $im_src, 0, 0, 0, 0, $w, $h, $width, $height);
  } else {
    $im = $im_src;
  }
  return $im;
}

$SymbolGroups = array();
function glyph_svg($key,$ver,$size,$line,$fill,$back, $colorize){
  global $SymbolGroups;
  if ($colorize) {
    if (count($SymbolGroups==0)) $SymbolGroups = loadSymbolGroups();
    $group = base2group($key);
    $line = $SymbolGroups[$group]['color'];
  }
  if (!$fill && $back) $fill = $back;
  if (!$ver) $ver=1;
  $base = substr($key,0,3);
  $file = 'iswa/svg' . $ver . '/' . $base . '/' . $key . '.svg';
  $im = file_get_contents($file);
  if ($line) $im = str_replace("#000000","#LINE",$im);
  if ($fill) $im = str_replace("#ffffff","#FILL",$im);
  if ($line) $im = str_replace("#LINE","#".$line,$im);
  if ($fill) $im = str_replace("#FILL","#".$fill,$im);
  return $im;
 }

function glyph_svg_trim($svg){
  $ipos= strpos($svg,"<g");
  $epos = strpos($svg,"/g>");
  $eepos = strpos($svg,"/g>",$epos+3);
  if ($eepos) $epos = $eepos;
  return substr($svg,$ipos,$epos-$ipos+3);
}

function glyph_txt($key,$ver,$line,$fill,$back,$break){
  if (!$ver) $ver=1;
  $base = substr($key,0,3);
  $file = 'iswa/txt' . $ver . '/' . $base . '/' . $key . '.txt';
  $im = file_get_contents($file);
  if ($back){
    $im = str_replace("-",$back,$im);
  } else {
    $im = str_replace("-"," ",$im);
  }
  if ($line) $im = str_replace("X",$line,$im);
  if ($fill) $im = str_replace("O",$fill,$im);
  if ($break) $im = str_replace("\n",$break,$im);
  return $im;
 }

function glyphogram_png($ksw, $ver,$size, $pad, $bound, $line, $fill, $back, $colorize){
  if ($colorize) $ver=4;
  if (!$ver) $ver=1;
  if($back==-1){
    $back='';
  } else {
    if ($ver==2 && !$back) {
      $back='000000';//black back for inverse
    }
    if (!$back) {
      $back='ffffff';
    }
  }
  if (!kswLayout($ksw)) {
    $im = imagecreatetruecolor(2,2);
  }
  if ($colorize){
    $line='';  //ignore line color
  }
  if (!$line){//default to black
    if ($ver==2){ 
      $line='FFFFFF';//white
    } else {
      $line='000000';//black
    }
    list($r,$g,$b) = array_values(str_split($line,2));
    $rl=hexdec($r);
    $gl=hexdec($g);
    $bl=hexdec($b);
    $line='';
  } else {
    list($r,$g,$b) = array_values(str_split($line,2));
    $rl=hexdec($r);
    $gl=hexdec($g);
    $bl=hexdec($b);
  }

  if ($fill){
    $trans_fill=0;
    list($r,$g,$b) = array_values(str_split($fill,2));
    $rf=hexdec($r);
    $gf=hexdec($g);
    $bf=hexdec($b);
  } else {
    $trans_fill=1;
    //check for white line
    if ($rl==255 and $gl==255 and $bl==255){
//      $fill="000000";
      $rf=0;
      $gf=0;
      $bf=0;
    } else {
      $rf=255;
      $gf=255;
      $bf=255;
    }
  }

  //start processing
  
/**
 * Step 1: process cluster string
 */
  $cluster = ksw2cluster($ksw);
  $max = str2koord($cluster[0][1]);
  $xMax = $max[0];
  $yMax = $max[1];
  $mins = cluster2min($cluster);
  $xMin = $mins[0];
  $yMin = $mins[1];

/**
 * Step 3: prep glyphs
 */
  foreach($cluster as $num=>$spatial){
    if ($num==0) continue;
    $base = substr($spatial[0],1,3);
    $key = substr($spatial[0],1,5);
    $image="im$num";
    $file = 'iswa/png' . $ver . '/' . $base . '/' . $key . '.png';
    $$image= imagecreatefromstring(file_get_contents($file)); 
    if ($line){
      //for solid images
      if (imagecolorstotal( $$image)==1){
        $index=0;
      } else {
        $index=1;
      }
      imageColorSet($$image,$index,$rl,$gl,$bl);
    }
    if ($fill){
      //2 for fill, 1 for line
      if (imagecolorstotal( $$image)==2){
        $index=0;
      } else {
        $index=2;
      }
      imageColorSet($$image,$index,$rf,$gf,$bf);
    }
  }

/**
 * Step 4: bound center, horizontal or vertical
 */
    if ($bound=="c" || $bound=="h"){
      if ((-$xMin) > ($xMax)) {
        $xMax = - $xMin;
      } else {
        $xMin = - $xMax;
      }
    }

    if ($bound=="c" || $bound=="v"){
      if ((-$yMin) > ($yMax)) {
        $yMax = - $yMin;
      } else {
        $yMin = - $yMax;
      }
    }


/**
 * Step 5: pad
 */
    $xMax+=$pad;
    $xMin-=$pad;
    $yMax+=$pad;
    $yMin-=$pad;

/**
 * Step 6: set up the base image
 */  
   if ( $xMax==0 && $xMin==0 && $yMax==0 && $yMin==0) {
     $xMax=2;
     $yMax=2;
   }
   
    $im_base = imagecreatetruecolor($xMax-$xMin, $yMax-$yMin);
    if ($back){
      sscanf($back, "%2x%2x%2x", $backR, $backG, $backB);
      $background = imagecolorallocate($im_base, $backR, $backG, $backB);
    } else {
      $background = imagecolorallocatealpha($im_base, 254, 0, 0,127);
    }
    imagefill($im_base, 0, 0, $background);
    imagealphablending($im_base, false); // turn off the alpha blending to keep the alpha channel
    imagesavealpha ($im_base, true );
      foreach($cluster as $num=>$spatial){
        if ($num==0) continue;
        $image="im$num";
        $W= ImageSX($$image);
        $H= ImageSY($$image);
        $coord = str2koord($spatial[1]);
        $X= $coord[0];
        $Y= $coord[1];

        ImageCopy($im_base, $$image, $X-$xMin, $Y-$yMin, 0, 0, $W, $H); 
        ImageDestroy($$image); 
    }

 /**
 * Step 7: ugly hack for transparent fills
 */  
    if ($trans_fill){
      for ($x=0;$x<$xMax-$xMin;$x++){
        for ($y=0;$y<$yMax-$yMin;$y++){
          $rgb = imagecolorat($im_base,$x,$y);
          $r = $rgb >> 16;
          $g = $rgb >> 8 & 255;
          $b = $rgb & 255;      
          if ($r==$rf and $g==$gf and $b==$bf){
            imagesetpixel($im_base, $x,$y, $background);
          }
        }
      }
    }

/**
 * Step 8: resize if needed
 */  
    if ($size){
      $width = imagesx($im_base);
      $height = imagesy($im_base);
      $w = ceil($width*$size);
      $h = ceil($height*$size);
      $im = imagecreatetruecolor($w, $h);

      $background = imagecolorallocate($im, 254, 0, 0);
      ImageColorTransparent($im, $background); // make the new temp image all transparent
 
      /* making the new image transparent */
      imagealphablending($im, false); // turn off the alpha blending to keep the alpha channel
      imagesavealpha ($im, true );
      imagecopyresampled($im, $im_base, 0, 0, 0, 0, $w, $h, $width, $height);
      ImageDestroy($im_base);
    } else {
      $im = $im_base;
    }
  return $im;
}

function glyphogram_svg($ksw, $ver, $size, $pad, $bound, $line, $fill, $back, $colorize){
  if (!$size) $size=1;
  if (!$ver) $ver=1;
  //start processing
  $cluster = ksw2cluster($ksw);

/**
 * Step 1: process cluster string
 */
  $cluster = ksw2cluster($ksw);
  $max = str2koord($cluster[0][1]);
  $xMax = $max[0];
  $yMax = $max[1];
  $mins = cluster2min($cluster);
  $xMin = $mins[0];
  $yMin = $mins[1];


/**
 * Step 3: prep glyphs
 */
  foreach($cluster as $num=>$spatial){
    if ($num==0) continue;
      $base = substr($spatial[0],1,3);
      $key = substr($spatial[0],1,5);
      $image="im$num";
      $$image = glyph_svg_trim(@glyph_svg($key,$ver,1,$line,$fill,$back,$colorize));//scrape out <g>'s
    }

/**
 * ERROR!
 * Step 4: bound center, horizontal or vertical
 */
    if ($bound=="c" || $bound=="h"){
      if ((-$xMin) > ($xMax)) {
        $xMax = - $xMin;
      } else {
        $xMin = - $xMax;
      }
    }

    if ($bound=="c" || $bound=="v"){
      if ((-$yMin) > ($yMax)) {
        $yMax = - $yMin;
      } else {
        $yMin = - $yMax;
      }
    }

/**
 * Step 5: pad
 */
    $xMax+=$pad;
    $xMin-=$pad;
    $yMax+=$pad;
    $yMin-=$pad;

/**
 * Step 6: transpose svg symbols
 */  
  //add symbols to base
  $svg = <<<EOT
<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN"
 "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">
<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
EOT;

  $W = $xMax - $xMin;
  $H = $yMax - $yMin;
  $svg .= ' width="' . $W*$size . '" height="' . $H*$size . '"';
  if ($back) $svg .= ' style="background-color: #' . $back . ';"';
  $svg .= '>' . "\n";

/*
  $svg .= <<<EOT
<metadata>
Valerie Sutton's ISWA 2010 symbols
transformed by Stephen E Slevinski Jr
combined using the SignWriting Image Server
</metadata>
EOT;
*/
  $svg .= "<metadata>" . $ksw . "</metadata>\n";
  if ($size!=1 && $size) $svg .= '<g transform="scale(' . $size . ')">';
  foreach($cluster as $num=>$spatial){
    if ($num==0) continue;
      $base = substr($spatial[0],1,3);
      $key = substr($spatial[0],1,5);
      $image="im$num";
      $coord = str2koord($spatial[1]);
      $X = $coord[0] - $xMin;
      $Y = $coord[1] - $yMin;
      $svg .= '<g transform="translate(' . $X . ',' . $Y . ')" >' . $$image . '</g>' . "\n";
  }
  if ($size!=1 && $size) $svg .= '</g>';
  $svg .= '</svg>';

  return $svg;
 }

function glyphogram_txt($ksw, $ver, $pad, $bound, $line, $fill, $back, $break) {
  if(!$ver) $ver=1;
  if (!kswLayout($ksw)) {
    return;
  }
  
/**
 * Step 1: process cluster string
 */
  $cluster = ksw2cluster($ksw);
  $max = str2koord($cluster[0][1]);
  $xMax = $max[0];
  $yMax = $max[1];
  $mins = cluster2min($cluster);
  $xMin = $mins[0];
  $yMin = $mins[1];


/**
 * Step 3: prep glyphs
 */
  foreach($cluster as $num=>$spatial){
    if ($num==0) continue;
    $base = substr($spatial[0],1,3);
    $key = substr($spatial[0],1,5);
    $image="im$num";
    $$image= @glyph_txt($key,$ver);
  }

/**
 * ERROR!
 * Step 4: bound center, horizontal or vertical
 */
    if ($bound=="c" || $bound=="h"){
      if ((-$xMin) > ($xMax)) {
        $xMax = - $xMin;
      } else {
        $xMin = - $xMax;
      }
    }

    if ($bound=="c" || $bound=="v"){
      if ((-$yMin) > ($yMax)) {
        $yMax = - $yMin;
      } else {
        $yMin = - $yMax;
      }
    }

/**
 * Step 5: pad
 */
    $xMax+=$pad;
    $xMin-=$pad;
    $yMax+=$pad;
    $yMin-=$pad;

/**
 * Step 6: set up the base image
 */  
    $im_lines = array();
    $im_lines = array_pad($im_lines,$yMax-$yMin,str_repeat(" ", $xMax-$xMin));
    //add symbols to base
    foreach($cluster as $num=>$spatial){
      if ($num==0) continue;
      $base = substr($spatial[0],1,3);
      $key = substr($spatial[0],1,5);
      $image="im$num";
      $W = strpos($$image,"\n");
      $len = strlen($$image);
      $H = (($len+1)/($W+1));
      $coord = str2koord($spatial[1]);
      $X= $coord[0];
      $Y= $coord[1];

      $glyph_lines = explode("\n",$$image);
      foreach ($glyph_lines as $line_num => $glyph_line){
        $im_line_num = $Y - $yMin + $line_num;
        $im_line_start = $X - $xMin;
        $im_line = $im_lines[$im_line_num];
        $im_line_pre = substr($im_line,0,$im_line_start);
        $remain = (($im_line_start + $W) - strlen($im_line));
        if ($remain<0) {
          $im_line_post = substr($im_line,$remain);
        } else {
          $im_line_post = '';
        }
        //ERROR! this combination ignores spaces as background
        $im_line_alt = substr($im_line,$im_line_start,$im_line_start +strlen($im_line));
        $im_line_new = '';
        //now merge $im_line_atl plus $glyph_line;
        for ($i=0;$i<strlen($glyph_line);$i++){
          $point = $glyph_line[$i];
          if ($point!="X" && $point!="O") {
            $point = $im_line_alt[$i];
          }
          $im_line_new .= $point;
        }
        $im_lines[$im_line_num] = $im_line_pre . $im_line_new . $im_line_post;

      }
    }


    $im = implode("\n",$im_lines);

    if ($back){
      $im = str_replace("-",$back,$im);
    } else {
      $im = str_replace("-"," ",$im);
    }
    if ($line) $im = str_replace("X",$line,$im);
    if ($fill) {
      $im = str_replace("O",$fill,$im);
    } else {
      $im = str_replace("O"," ",$im);
    }
    if ($break) $im = str_replace("\n",$break,$im);
  return $im;
 }

?>
