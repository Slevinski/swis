<?php
/**
 * Kartesian SignWrting library
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
 * @brief Kartesian SignWriting irregular display variants
 * @file
 *   
 */

/** @defgroup ksw Kartesian SignWriting
 *  Irregular display variants
 */

/** @defgroup kre Regular Expressions 
 *  @ingroup ksw
 *  Regular expressions for irregular variant display
 */
 
/** @defgroup kscrape Text Scraping
 *  @ingroup ksw
 *  Scrape text from Kartesian SignWriting
 */

/** @defgroup ktrans Transformations
 *  @ingroup ksw
 *  Transform various forms of Kartesian SignWriting
 */

/** 
 * @brief test if text is Kartesian SignWriting without preprocessed information 
 * @param $text character string
 * @return boolean value if text is Kartesian SignWriting without preprocessed information
 * @ingroup kre
 */
 function kswRaw($text){
  $ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  $ksw_coord = 'n?[0-9]+xn?[0-9]+';
  $ksw_word = '(A(' . $ksw_sym. ')+)?[BLMR](' . $ksw_sym . $ksw_coord . ')*';
  $ksw_punc = 'S38[7-9ab][0-5][0-9a-f]';
  $ksw_pattern = '/^(' . $ksw_word . '|' . $ksw_punc . ')( ' . $ksw_word . '| ' . $ksw_punc .')*$/i';
  $result = preg_match($ksw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return true;
    }
  }
  return false;
}

/** 
 * @brief test if text is Kartesian SignWriting with symbol sizes 
 * @param $text character string
 * @return boolean value if text is Kartesian SignWriting with symbol sizes
 * @ingroup kre
 */
function kswExpand($text){
  $ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  $ksw_coord = 'n?[0-9]+xn?[0-9]+';
  $ksw_pcoord = '[0-9]+x[0-9]+';
  $ksw_word = '(A(' . $ksw_sym. ')+)?[BLMR](' . $ksw_sym . $ksw_pcoord . 'x' . $ksw_coord . ')*';
  $ksw_punc = 'S38[7-9ab][0-5][0-9a-f]' . $ksw_pcoord;
  $ksw_pattern = '/^(' . $ksw_word . '|' . $ksw_punc . ')( ' . $ksw_word . '| ' . $ksw_punc .')*$/i';
  $result = preg_match($ksw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return true;
    }
  }
  return false;
}

/** 
 * @brief test if text is Kartesian SignWriting with preprocessed information 
 * @param $text character string
 * @return boolean value if text is Kartesian SignWriting with preprocessed information
 * @ingroup kre
 */
function kswLayout($text){
  $ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  $ksw_coord = 'n?[0-9]+xn?[0-9]+';
  $ksw_pcoord = '[0-9]+x[0-9]+';
  $ksw_word = '(A(' . $ksw_sym. ')+)?[BLMR](' . $ksw_pcoord . ')(' . $ksw_sym . $ksw_coord . ')*';
  $ksw_punc = 'S38[7-9ab][0-5][0-9a-f]' . $ksw_coord;
  $ksw_pattern = '/^(' . $ksw_word . '|' . $ksw_punc . ')( ' . $ksw_word . '| ' . $ksw_punc .')*$/i';
  $result = preg_match($ksw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return true;
    }
  }
  return false;
}

/** 
 * @brief test if text is Kartesian SignWriting for panel 
 * @param $text character string
 * @return boolean value if text is Kartesian SignWriting for panel
 * @ingroup kre
 */
function kswPanel($text){
  $ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  $ksw_coord = 'n?[0-9]+xn?[0-9]+';
  $ksw_pcoord = '[0-9]+x[0-9]+';//always positive
  $ksw_word = '[BLMR](' . $ksw_pcoord . ')(' . $ksw_sym . $ksw_coord . ')*';
  //punctuationis wrapped in signbox...
//  $ksw_punc = 'S38[7-9ab][0-5][0-9a-f]' . $ksw_pcoord;
//  $ksw_panel = 'D' . $ksw_pcoord . '(_' . $ksw_word . '|_' . $ksw_punc .')*';
  $ksw_panel = 'D' . $ksw_pcoord . '(_' . $ksw_word . ')*';
  $ksw_pattern = '/^' . $ksw_panel . '( ' . $ksw_panel .')*$/i';
  $result = preg_match($ksw_pattern,$text,$matches);
  if ($result) {
    if ($text == $matches[0]) {
      return true;
    }
  }
  return false;
}

/** 
 * @brief scrape symbol keys from KSW
 * @param $ksw ksw string
 * @return a string of symbol keys
 * @ingroup kscrape
 */
 function ksw2key($ksw){
  $keys = '';
  $sym_pattern = '/S[123][0-9a-f]{2}[0-5][0-9a-f]n?[0-9]+xn?[0-9]+/i';
  preg_match_all($sym_pattern,$ksw, $matches);
  foreach($matches[0] as $spatial){
    $keys .= substr($spatial,0,6);
  }
  return $keys;
}

/** 
 * @brief transform ksw layout string to array of symbols with placement
 * @param $ksw ksw layout string
 * @return array of symbols with positioning
 * @ingroup kscrape
 */
 function ksw2cluster($ksw){
  $cluster = array();
  if (!$ksw) {
    $cluster[0][0]="M";
    $cluster[0][1]="0x0";
    return $cluster;
  }
  $seq = 'A' . ksw2seq($ksw);
  $ksw = str_replace($seq,'',$ksw);
  if(isPunc($ksw)) {
    $ksw_pattern = '/S38[7-9ab][0-5][0-9a-f]n?[0-9]+xn?[0-9]+/i';
    preg_match($ksw_pattern,$ksw,$match);
    $ksw = $match[0];
    $sym = substr($ksw,0,6);
    $len = strlen ($ksw);
    $strnum = substr($ksw,6,$len-6);
    $coord = str2koord($strnum);
    $strmax = koord2str(-1*$coord[0],-1*$coord[1]); 
    $cluster[] = array("B",$strmax);
    $cluster[] = array($sym,$strnum);
  } else {
    $ksw_pattern = '/[BLMR]([0-9]+x[0-9]+)?(S[123][0-9a-f]{2}[0-5][0-9a-f]n?[0-9]+xn?[0-9]+)*/i';
    preg_match($ksw_pattern,$ksw,$match);
    $strnum = $match[1];
    $cluster[] = array($match[0][0],$strnum);
    if(!$strnum){
      $pos = 1;
    } else {
      $pos = strpos($ksw,$strnum) + strlen($strnum);
    }
    $len = strlen($ksw);
    $syms = substr($ksw,$pos,$len-$pos);
    $sym_pattern = '/S[123][0-9a-f]{2}[0-5][0-9a-f]n?[0-9]+xn?[0-9]+/i';
    preg_match_all($sym_pattern,$syms, $matches);
    foreach($matches[0] as $spatial){
      $sym = substr($spatial,0,6);
      $len = strlen($spatial);
      $strnum=substr($spatial,6,$len-6);
      $cluster[] = array($sym,$strnum);
    }
  }
  return $cluster;
}

/** 
 * @brief transform ksw expanded string to array of symbols with placement
 * @param $ksw ksw expanded 
 * @return array of symbols with positioning
 * @ingroup kscrape
 */
function expand2cluster($ksw){
  $cluster = array();
  if (!$ksw) {
    $cluster[0][0]="M";
    $cluster[0][1]="0x0";
    return $cluster;
  }
  if(isPunc($ksw)) {
    $ksw_pattern = '/S38[7-9ab][0-5][0-9a-f]n?[0-9]+xn?[0-9]+/i';
    preg_match($ksw_pattern,$ksw,$match);
    $ksw = $match[0];
    $sym = substr($ksw,0,6);
    $len = strlen ($ksw);
    $strnum = substr($ksw,6,$len-6);
    $coord = str2koord($strnum);
    $strmax = koord2str(-1*$coord[0],-1*$coord[1]); 
    $cluster[] = array("M",$strmax);
    $cluster[] = array($sym,$strnum);
  } else {
    $ksw_pattern = '/[BLMR](S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]+x[0-9]+xn?[0-9]+xn?[0-9]+)*/i';
    preg_match($ksw_pattern,$ksw,$match);
    $cluster[] = array($match[0][0],'');
    $pos = 0;
    $len = strlen($ksw);
    $syms = substr($ksw,$pos,$len-$pos);
    $sym_pattern = '/S[123][0-9a-f]{2}[0-5][0-9a-f]?[0-9]+x?[0-9]+xn?[0-9]+xn?[0-9]+/i';
    preg_match_all($sym_pattern,$syms, $matches);
    foreach($matches[0] as $spatial){
      $sym = substr($spatial,0,6);
      $len = strlen($spatial);
      $strnum=substr($spatial,6,$len-6);
      $cluster[] = array($sym,$strnum);
    }
  }
  return $cluster;
}

/** 
 * @brief transform panel offset string to array of symbols with placement
 * @param $ksw ksw panel offset string
 * @return array of symbols with positioning
 * @ingroup kscrape
 */
function offset2cluster($ksw){
  $ksw_pattern = '/[BLMR]([0-9]+x[0-9]+)(S[123][0-9a-f]{2}[0-5][0-9a-f]n?[0-9]+xn?[0-9]+)*/i';
  preg_match($ksw_pattern,$ksw,$match);
  $cluster = array();
  $strnum = $match[1];
  $adj = str2koord($strnum);
  $pos = strpos($ksw,$strnum) + strlen($strnum);
  $len = strlen($ksw);
  $syms = substr($ksw,$pos,$len-$pos);

  $sym_pattern = '/S[123][0-9a-f]{2}[0-5][0-9a-f]n?[0-9]+xn?[0-9]+/i';
  preg_match_all($sym_pattern,$syms, $matches);
  foreach($matches[0] as $spatial){
    $sym = substr($spatial,0,6);
    $len = strlen($spatial);
    $strnum=substr($spatial,6,$len-6);
    $coord = str2koord($strnum);
    $coord[0] += $adj[0];
    $coord[1] += $adj[1];
    $cluster[] = array($sym,koord2str($coord[0],$coord[1]));
  }
  return $cluster;
}

/** 
 * @brief transform panel string to array of symbols with placement
 * @param $ksw ksw panel string
 * @return array of symbols with positioning
 * @ingroup kscrape
 */
function panel2cluster($ksw){
  $ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  $ksw_coord = '[0-9]+x[0-9]+';//always positive
  $ksw_ncoord = 'n?[0-9]+xn?[0-9]+';
  $ksw_word = '[BLMR](' . $ksw_coord . ')(' . $ksw_sym . $ksw_ncoord . ')*';
  $ksw_punc = 'S38[7-9ab][0-5][0-9a-f]' . $ksw_coord;
  $ksw_panel = 'D(' . $ksw_coord . ')(_' . $ksw_word . '|_' . $ksw_punc .')*';
  $ksw_pattern = '/' . $ksw_panel . '/i';

  preg_match($ksw_pattern,$ksw,$matches);
  $cluster = array();
  $strnum = $matches[1];
  $cluster[] = array($matches[0][0],$strnum);
  $pos = strpos($ksw,$strnum) + strlen($strnum);
  $len = strlen($ksw);
  $syms = substr($ksw,$pos,$len-$pos);

  $ksw_pattern = '/_(' . $ksw_word . '|' . $ksw_punc .')/i';
  preg_match_all($ksw_pattern,$matches[0], $matches);
  $syms = array();
  $syms[]=array('M',$strnum);
  foreach($matches[0] as $unit){
    $cluster = array();
    $unit = str_replace('_','',$unit);
    $cluster = offset2cluster($unit);
    $syms = array_merge($syms,$cluster);
  }
  return $syms;
}

/** 
 * @brief transform cluster symbol array into ksw layout string
 * @param $cluster array of symbols with positioning
 * @return ksw layout string
 * @ingroup kscrape
 */
function cluster2ksw($cluster){
  $first = $cluster[0];
  $ksw = $first[0] . $first[1];
  for ($i=1;$i<count($cluster);$i++){
    $spatial = $cluster[$i];
    $ksw .= $spatial[0] . $spatial[1];
  }
	  return $ksw;
}

/** 
 * @brief find minimum coordinate of symbol cluster
 * @param $cluster array of symbols with positioning
 * @return array of x, y values
 * @ingroup kscrape
 */
function cluster2min($cluster){
  foreach($cluster as $i=>$sym){
    if ($i==0) continue;
    $coord = str2koord($sym[1]);
    if ($i==1){
      $xMin = $coord[0];
      $yMin = $coord[1];
    } else {
      $xMin = min($xMin,$coord[0]);
      $yMin = min($yMin,$coord[1]);
    }
  }
  if ($xMin > 0) {
    $xMin=0;
    $yMin=0;
  }
  return array($xMin,$yMin);
}

/** 
 * @brief scrape prefix of symbol keys
 * @param $ksw ksw string
 * @return string of symbol keys
 * @ingroup kscrape
 */
 function ksw2seq($ksw){
  $ksw_pattern = '/A(S[123][0-9a-f]{2}[0-5][0-9a-f])+/i';
  preg_match($ksw_pattern,$ksw,$match);
  if ($match){
    $seq = $match[0];
    $len = strlen($seq);
    $seq = substr($seq,1,$len-1);
    return $seq;
  }
}

/**
 * @brief strip preprocessing information
 * @param $ksw ksw layout string
 * @return ksw raw string without preprocessed information
 * @ingroup ktrans
 */
function ksw2raw($ksw){
  $output = array();
  $words = explode(' ',trim($ksw));

  foreach ($words as $word){
    if (isPunc($word)) {
      $len = strlen ($word);
      $output[] = substr($word,0,6);
    } else {
      $outksw = '';
      $seq = ksw2seq($word);
      if ($seq) {
        $outksw .= 'A';
        $asyms = str_split($seq,6);
        foreach ($asyms as $asym){
          $outksw  .= $asym;
        }
      }
      $cluster = ksw2cluster($word);
      $outksw .= $cluster[0][0];
      //now additional coords
      for($i=1;$i<count($cluster);$i++){
        $outksw .= $cluster[$i][0] . $cluster[$i][1];
      }
      $output[] = $outksw;
    }
  }
  $raw = implode($output,' ');
  return $raw;
}

/**
 * @brief expand symbols with size information
 * @param $ksw ksw raw string
 * @return ksw expanded string with symbol sizes
 * @ingroup ktrans
 */
function ksw2expand($ksw){
  global $sym_sizer;
  if(!$sym_sizer) sym_init();
  $sym_sizer->load($ksw);
  $output = array();
  $words = explode(' ',trim($ksw));

  foreach ($words as $word){
    if (isPunc($word)) {
      $len = strlen ($word);
      $output[] = str_replace("xX",'',$sym_sizer->expand(substr($word,0,6)) . 'X');
    } else {
      $outksw = '';
      $seq = ksw2seq($word);
      if ($seq) {
        $outksw .= 'A';
        $asyms = str_split($seq,6);
        foreach ($asyms as $asym){
          $outksw  .= $asym;
        }
      }
      $cluster = ksw2cluster($word);
      $outksw .= $cluster[0][0];
      //now additional coords
      for($i=1;$i<count($cluster);$i++){
        $outksw .= $sym_sizer->expand($cluster[$i][0]) . $cluster[$i][1];
      }
      $output[] = $outksw;
    }
  }
  $raw = implode($output,' ');
  return $raw;
}

/**
 * @brief center sign and preprocess max coordinate
 * @param $ksw ksw raw string
 * @return ksw layout string with preprocesed max coordinate
 * @ingroup ktrans
 */
function raw2ksw($raw){
  global $sym_sizer;
  if(!$sym_sizer) sym_init();
  $sym_sizer->load($raw);
  $ksw = '';

  $output = array();
  $words = explode(' ',trim($raw));

  $ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  $ksw_coord = 'n?[0-9]+xn?[0-9]+';
  $ksw_syms = $ksw_sym . $ksw_coord . 'x' . $ksw_coord;
  $ksw_pattern = '/' . $ksw_syms . '/i';

  foreach ($words as $word){
    if (isPunc($word)) {
      $expanded = $sym_sizer->expand($word);
      $len = strlen($expanded);
      $size = substr($expanded,6,$len-6);
      $coord = str2koord($size);
      $output[] = $word . koord2str(-intval($coord[0]/2),-intval($coord[1]/2));
    } else {
      $outksw = '';
      $seq = ksw2seq($word);
      if ($seq) {
        $outksw .= 'A' . $seq;
        $word = str_replace($outksw,'',$word);
      }
      $cluster = ksw2cluster($word);
      $outksw .= $cluster[0][0];

      $expanded = $sym_sizer->expand($word);

      preg_match_all($ksw_pattern,$expanded, $matches);
      $syms = array();
      $Tcentering=0;//trunk centering
      $Hcentering=0;//head centering
      foreach($matches[0] as $spatial){
        $sym = substr($spatial,0,6);
        $base = substr($sym,1,3);
        if (isTrunk($base)) $Tcentering++;
        if (isHead($base)) $Hcentering++;
        $len = strlen($spatial);
        $nums = explode('x', substr($spatial,6,$len-6));
        $size = str2koord($nums[0] . 'x' . $nums[1]);
        $min = str2koord($nums[2] . 'x' . $nums[3]);
        $syms[] = array($sym,$nums[2] . 'x' . $nums[3],koord2str($size[0]+$min[0],$size[1]+$min[1]));
      }

      $X_max = array();
      $cX_min = array();
      $cX_max = array();

      $Y_max = array();
      $cY_min = array();
      $cY_max = array();
      foreach($syms as $sym){
        $min = str2koord($sym[1]);
        $max = str2koord($sym[2]);
        $X_max[] = $max[0];
        $Y_max[] = $max[1];
        //if neither center, add all syms
        if (!$Tcentering && !$Hcentering) {
          $cX_min[] = $min[0];
          $cY_min[] = $min[1];
          $cX_max[] = $max[0];
          $cY_max[] = $max[1];
          continue;
        }

        //if not head centering, add all y centering
        if (!$Hcentering) {
          $cY_min[] = $min[1];
          $cY_max[] = $max[1];
        }
        
        //if trunk, add x centering
        if (isTrunk($sym[0])){
          $cX_min[] = $min[0];
          $cX_max[] = $max[0];
        }
        
        //if head, add both centering...
        if (isHead($sym[0])){
          $cX_min[] = $min[0];
          $cY_min[] = $min[1];
          $cX_max[] = $max[0];
          $cY_max[] = $max[1];
        }
    
      }
      $center = array(intval((max($cX_max) + min($cX_min))/2),intval((max($cY_max) + min($cY_min))/2));
      $max = array(max($X_max)-$center[0],max($Y_max)-$center[1]);
      //go throu syms again.

      $outksw .= koord2str($max[0],$max[1]);
      foreach($syms as $sym){
        $min = str2koord($sym[1]);
        $strnum = koord2str($min[0]-$center[0],$min[1]-$center[1]);
        $outksw .= $sym[0] . koord2str($min[0] - $center[0], $min[1] - $center[1]);
      }

      $output[] = $outksw;
    }
  }
  $ksw = implode($output,' ');
  return $ksw;
}
/**
 * @brief add visual reference for the center of a signbox
 * @param $ksw ksw layout string
 * @param $out distance from sign edge to crosshairs
 * @param $adj min coordinate array
 * @return ksw layout string
 * @ingroup ktrans
 */
function crosshairs ($ksw,$out=15,$adj=''){
    $cluster = ksw2cluster($ksw);
    $min = cluster2min($cluster);
    $max = str2koord($cluster[0][1]);
    $clusterW = $max[0]-$min[0];
    $clusterH = $max[1]-$min[1];
    $zeroX = max(-$min[0],$clusterW+$min[0]);
    $zeroY = max(-$min[1],$clusterH+$min[1]);

    if (is_array($adj)){
      $moveX = $min[0]-$adj[0];
      $moveY = $min[1]-$adj[1];
    } else {
      $moveX = 0;//$min[0];
      $moveY = 0;//$min[1];
    }
    $zeroX -= $moveX;
    $zeroY -= $moveY;

    //add top mark
    $x = $moveX-1;
    $y = $moveY - $zeroY - 12 - $out;
    $spatial = array("S37c00",koord2str($x,$y));
    $cluster[]=$spatial;
    //add bottom mark
    $x = $moveX-1;
    $y = $moveY + $zeroY + $out ;
    $spatial = array("S37c00",koord2str($x,$y));
    $cluster[]=$spatial;

    //add left mark
    $x= $moveX - $zeroX - 12 - $out;
    $y= $moveY-1;
    $spatial = array("S37c06",koord2str($x,$y));
    $cluster[]=$spatial;
    //add right mark
    $x = $moveX + $zeroX + $out;
    $y = $moveY-1;
    $spatial = array("S37c06",koord2str($x,$y));
    $cluster[]=$spatial;

    $ksw = cluster2ksw($cluster);
    $ksw = raw2ksw($ksw);
    return $ksw;
}

/**
 * @brief create ksw string with tight bounding box
 * @param $ksw ksw panel string
 * @return ksw layout string
 * @ingroup ktrans
 */
 function panelTrim($ksw){
  $layout = raw2ksw(cluster2ksw(panel2cluster($ksw)));
  return $layout;
}


/**
 * @brief create ksw panel string for specific length
 * @param $ksw ksw layout string
 * @param $length length of column or row
 * @param $params array of transformational parameters
 * @param $params['width'] width of column or row
 * @param $params['padding'] distance from closest symbol to width edge
 * @param $params['form'] col or row: form of panel
 * @param $params['style'] fix or flex: form of panel
 * @param $params['signTop'] padding before a sign
 * @param $params['signBottom'] padding after a sign
 * @param $params['puncTop'] padding before a punctuation
 * @param $params['puncBottom'] padding after a punctuation
 * @param $params['offset'] offset for lanes or channels
 * @param $params['top'] padding on top of column or row before sign
 * @param $params['justify'] 1, 2, or 3: Justify 1 pulls punctuation to the end of a column or row by moving signs closer together. Justify 2 pushes sign apart to evenly cover a column or row. Justify 3 will both pull punctuation and push signs.  
 * @return ksw panel string
 * @ingroup ktrans
 */
function ksw2panel($ksw,$length,$params=array()){
  $width = 0;
  $padding=15;
  if (isVert($ksw)){
    $form='col';
  } else {
    $form = 'row';
  }
  $style='flex';
  $spacing=12;
  $offset=50;
  $top=0;
  $justify=3; //0 does nothing, 1 pulls punc, 2 pushes signs, 3 does doth
  $signTop = 20;
  $signBottom = 20;
  $puncTop = 0;
  $puncBottom = 30;
  $valid = array('width','padding','form','style','signTop','signBottom','puncTop','puncBottom','offset','top','justify');
  foreach ($params as $param=>$value){
    if (in_array($param,$valid)) {
      $$param = $value;
    }
  }
  
  //rotate if vert row or non-vert col
  if (isVert($ksw) && $form=='row'){
    $ksw = reorient($ksw);
  }
  if (!isVert($ksw) && $form=='col'){
    $ksw = reorient($ksw);
  }
  
  if ($form=='col'){
    $cl_h0 = 'cl_y0';
    $cl_h1 = 'cl_y1';
    $cl_w0 = 'cl_x0';
    $cl_w1 = 'cl_x1';
  } else {
    $cl_h0 = 'cl_x0';
    $cl_h1 = 'cl_x1';
    $cl_w0 = 'cl_y0';
    $cl_w1 = 'cl_y1';
  }
  
  $panels = array();
  $workspace = array();  //array of cluster arrays
  $top_h = $top; 
  $wMin = array();
  $wMax = array();
  $words = explode(" ",trim($ksw));
  for ($i=0;$i<count($words);$i++){
    $ksw = $words[$i];
    if (!$ksw) continue;
    if (isPunc($ksw)) { 
      $prepad = $puncTop;
      $postpad = $puncBottom;
    } else {
      $prepad = $signTop;
      $postpad = $signBottom;
    }
    $cluster = ksw2cluster($ksw);
    $cl_min = cluster2min($cluster);
    $cl_x0 = $cl_min[0];
    $cl_y0 = $cl_min[1];
    $cl_max = str2koord($cluster[0][1]);
    $cl_x1 = $cl_max[0];
    $cl_y1 = $cl_max[1];
    $clear=0;

//IF 1 - to add or not to add
    if ((($prepad + $top_h + ($$cl_h1-$$cl_h0)) < $length) || count($workspace)==0){  //no need to add post pad...
      // get column ready for addition
      //set new top
      $top_h += $prepad;
      if ($form=='row' && hasHead($ksw)){
        $cl_y0 -= $offset;
        $cl_y1 -= $offset;
      }
      switch ($cluster[0][0]){
        case "L":
          $cl_x0 -= $offset;
          $cl_x1 -= $offset;
          break;
        case "R":
          $cl_x0 += $offset;
          $cl_x1 += $offset;
          break;
      }
      $wMin[] = $$cl_w0;
      $wMax[] = $$cl_w1;
      if ($form=='col'){
        $cluster[0][1]=koord2str($cl_x0,$top_h);
      } else {
        $cluster[0][1]=koord2str($top_h,$cl_y0);
      }
      $workspace[]=$cluster;
      $top_h += ($$cl_h1-$$cl_h0);
      $top_h += $postpad;
    } else {
      $clear = 1;
      //pull punc in
      //check style...
      if (isPunc($cluster[1][0]) && ($justify==1 || $justify==3)) {
        $diff = ($prepad + $top_h  + ($$cl_h1-$$cl_h0) - $length);
        $diff = intval(($diff-.001)/(count($workspace)-1)) + 1;
        $punc = $cluster;
//        $i++;
        foreach($workspace as $j=>$cluster){
          if ($j==0) continue;
          $coord = str2koord($cluster[0][1]);
          if ($form=='col'){
            $workspace[$j][0][1]=koord2str($coord[0],$coord[1]-$diff*$j);
          } else {
            $workspace[$j][0][1]=koord2str($coord[0]-$diff*$j,$coord[1]);
          }
          $top_h -=$diff;
        }
        
        $wMin[] = $$cl_w0;
        $wMax[] = $$cl_w1;
        $top_h += $prepad;
        if ($form=='col'){
          $punc[0][1]=koord2str($cl_x0,$top_h);
        } else {
          $punc[0][1]=koord2str($top_h,$cl_y0);
        }
        $workspace[]=$punc;
        $top_h += $$cl_h1 - $$cl_h0;
      } else {
        $i--;
      }
      
    }

//IF 2 - to clear or not to clear
    if (($clear==1) || ($i==(count($words)-1))){  //finalize column: finished or last
      $clear=0;
      //format & add
      $wMin = min($wMin);
      $wMax = max($wMax);
      
  //IF 2A - justify column;
      if  ( ($i<(count($words))-1) && ($justify==2 || $justify==3)) {
        $diff = $length - $top_h;
        $diff = intval(($diff-.001)/(count($workspace)-1));
        if ($diff){
          foreach($workspace as $j=>$cluster){
            if ($j==0) continue;
            $coord = str2koord($cluster[0][1]);
            if ($form=='col'){
              $workspace[$j][0][1]=koord2str($coord[0],$coord[1]+$diff*$j);
            } else {
              $workspace[$j][0][1]=koord2str($coord[0]+$diff*$j,$coord[1]);
            }
          }
        }
      }
  //IF 2B - adjust and center width
      $real_w = $wMax - $wMin;
      $pad_w = $real_w + $padding*2;
      $extra = $width - $real_w;
      
      if ($style=="fix"){
        if ($extra<0) $extra=0;
        $w = $width;
        $adj = intval($extra/2) - $wMin;
    
      } else { //flex
        if ($real_w < $width) {
          $w = $width;
          $adj = intval($extra/2) - $wMin;
        } else {
          if ($extra<0 && $width>0) {
            $w = $real_w;
            $adj = - $wMin;
          } else {
            $w = $pad_w;
            $adj = $padding - $wMin;
          }
        }
      }

      if ($form=='col'){
        $panel = "D" . koord2str($w,$length);
      } else {
        $panel = "D" . koord2str($length,$w);
      }
      foreach($workspace as $cluster){
        $co_offset = str2koord($cluster[0][1]);
        if ($form=='col'){
          $co_offset[0] += $adj;
        } else {
          $co_offset[1] += $adj;
        }
        $cl_min = cluster2min($cluster);
        $co_offset[0] -= $cl_min[0];
        $co_offset[1] -= $cl_min[1];
        $cluster[0][1]=koord2str($co_offset[0],$co_offset[1]);
        $panel .= '_' . cluster2ksw($cluster);
      }
      $panels[]=$panel;

    //IF 2C - rewind
      if ($i<(count($words))-1){
//        $i--;
        $top_h = $top;
        $wMin = array();
        $wMax = array();
        $workspace=array();
      }
    }
  }
  return implode($panels,' ');
}

?>
