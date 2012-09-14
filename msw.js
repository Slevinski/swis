/**
 * Modern SignWriting Library for JavaScript
 * 
 * Copyright 2007-2012 Stephen E Slevinski Jr
 * Steve (Slevin@signpuddle.net)
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
 * @author Steve (slevin@signpuddle.net)  
 * @license http://www.opensource.org/licenses/gpl-3.0.html GPL
 * @access public
 * @version 2.0
 * @filesource
 *   
 */
sym_write = 'S10000';

/**
 * Binary SignWriting encoding for symbols, numbers, and markers
 */
String.prototype.chunk = function(n) {
  if (typeof n=='undefined') n=2;
  return this.match(RegExp('.{1,'+n+'}','g'));
};

function base2view(base){
  var view = base + '00';
  if (isHand(base)){
    if(!isSymGrp(base)){
      view = base + '10';
    }
  }
  return view;
}

var sg_list = new Array('100','10e','11e','144','14c','186','1a4','1ba','1cd','1f5', '205','216','22a','255','265','288','2a6','2b7','2d5','2e3', '2f7', '2ff','30a','32a','33b','359', '36d','376', '37f', '387');
var sg_colorize = new Array('0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'FF0099', '006600', '006600', '006600', '006600', '006600', '000000', '000000', '884411', 'FF9900');
function base2group(base){
  var sg_list = new Array('100','10e','11e','144','14c','186','1a4','1ba','1cd','1f5', '205','216','22a','255','265','288','2a6','2b7','2d5','2e3', '2f7', '2ff','30a','32a','33b','359', '36d','376', '37f', '387');
  var prev;
  for(var i=0;i<sg_list.length;i++) {
    var group = sg_list[i];
    if (hexdec(base)==hexdec(group)) return group;
    if (hexdec(base)<hexdec(group)) return prev;
    prev = group;
  }
  return group;
}

function isSymGrp(char){
  var symgrp = sg_list.indexOf(char);
  if (symgrp == -1) {
    return false;
  } else {
    return true;
  } 
}

var cat_list = new Array('100','205','2f7', '2ff','36d','37f','387');
function base2cat(base){
  var cat_list = new Array('100','205','2f7', '2ff','36d','37f','387');
  var cat, prev;
  for(var i=0;i<cat_list.length;i++) {
    cat = cat_list[i];
    if (hexdec(base)==hexdec(cat)) return cat;
    if (hexdec(base)<hexdec(cat)) return prev;
    prev = cat;
  }
  return cat;
}

function isKey(key){
  return /^S?[123][a-f0-9]{2}[012345][a-f0-9]$/i.test(key);
}

function dechex(d) {return d.toString(16);}

function hexdec(h) {return parseInt(h,16);} 

function key2code(key){
  key = key.replace('S','');
  return ((hexdec(key.slice(0,3)) - 256) * 96) + ((hexdec(key.slice(3,4)))*16) + hexdec(key.slice(4,5)) + 1;
}
//symbol section
function code2key(code){
  var base = parseInt(code/96);
  code = code - base*96;
  var fill = parseInt((code-1)/16);
  var rot = code - (fill*16);
  if (fill==0 && rot==0) {
    base--;
    fill=5;
    rot=16;
  }
  return dechex(base+256) + dechex(fill) + dechex(rot-1);
}

inHexRange = function(start,end,char){
  char = char.replace('S','').slice(0,3);
  return (hexdec(start)<=hexdec(char) && hexdec(end)>=hexdec(char)); 
}

isISWA = function (char) {return inHexRange("100","38b",char);}
isWrit = function (char) {return inHexRange("100","37e",char);}
isHand = function (char) {return inHexRange("100","204",char);}
isMove = function (char) {return inHexRange("205","2f6",char);}
isDyn = function (char) {return inHexRange("2f7","2fe",char);}
isHead = function (char) {return inHexRange("2ff","36c",char);}
isTrunk = function (char) {return inHexRange("36d","375",char);}
isLimb = function (char) {return inHexRange("376","37e",char);}
isLoc = function (char) {return inHexRange("37f","386",char);}
isPunc = function (char) {return inHexRange("387","38b",char);} 
isFill = function (char) {return inHexRange("38c","391",char); }
isRot = function (char) {return inHexRange("392","3a1",char); }


function char2fill(char){
  return dechex(hexdec(char)-hexdec('110'));
}

function fill2char(fill){
  return dechex(hexdec(fill)+hexdec('110'));
}

function char2rot(char){
  return dechex(hexdec(char)-hexdec('120'));
}

function rot2char(rot){
  return dechex(hexdec(rot)+hexdec('120'));
}

function key2bsw(key){
  key = key.replace("S",'');
  var base = dechex(hexdec(key.slice(0,3))  + hexdec('30'));
  var fill = key.slice(3,4);
  var rot = key.slice(4,5);
  return base + fill2char(fill) + rot2char(rot);
}

function bsw2key(bsw){
  var base = dechex(hexdec(bsw.slice(0,3)) - hexdec('30'));
  var fill = bsw.slice(3,6);
  var rot = bsw.slice(6,9);
  return base + char2fill(fill) + char2rot(rot);
}


/**
 * Number section
 */
function koord2str(x,y){
  var str = '';
  if (x<0) str += 'n';
  str += Math.abs(x);
  str += 'x';
  if (y<0) str += 'n';
  str += Math.abs(y);
  return str;
}

function str2koord(str) {
  str = str.replace(/n/gi,'-');
  var parts = str.split('x');
  var x = parseInt(parts[0]);
  var y = parseInt(parts[1]);
  return [x,y];
} 

function coord2str(x,y){
  var str = '';
  str += (500+x);
  str += 'x';
  str += (500+y);
  return str;
}

function str2coord(str) {
//  if (!str) return [0,0];
  var parts = str.split('x');
  var x = parseInt(parts[0]) - 500;
  var y = parseInt(parts[1]) - 500;
  return [x,y];
} 

function num2bsw(num) {
  return dechex(num + hexdec('800'));
}

function bsw2num(bsw) {
  return hexdec(bsw) - hexdec('800');
}

function coord2bsw(arr) {
  return num2bsw(arr[0]) + num2bsw(arr[1]);
}

function bsw2coord(bsw) {
  return [bsw2num(bsw.slice(0,3)),bsw2num(bsw.slice(3,6))];
}

/**
 * Character SignWriting encoding with Unicode characters
 */
//following functions from http://0xcc.net/
function escapeToUtf16(str) {
  var escaped = ''
  for (var i = 0; i < str.length; ++i) {
    var hex = str.charCodeAt(i).toString(16).toUpperCase();
    escaped += "\\u" + "0000".substr(hex.length) + hex;
  }
  return escaped;
}
function convertEscapedCodesToCodes(str, prefix, base, num_bits) {
  var parts = str.split(prefix);
  parts.shift();  // Trim the first element.
  var codes = [];
  var max = Math.pow(2, num_bits);
  for (var i = 0; i < parts.length; ++i) {
    var code = parseInt(parts[i], base);
    if (code >= 0 && code < max) {
      codes.push(code);
    } else {
      // Malformed code ignored.
    }
  }
  return codes;
}
function convertEscapedUtf32CodesToUnicodeCodePoints(str) {
  return convertEscapedCodesToCodes(str, "\\U", 16, 32);
}
function convertUnicodeCodePointsToUtf16Codes(unicode_codes) {
  var utf16_codes = [];
  for (var i = 0; i < unicode_codes.length; ++i) {
    var unicode_code = unicode_codes[i];
    if (unicode_code < (1 << 16)) {
      utf16_codes.push(unicode_code);
    } else {
      var first = ((unicode_code - (1 << 16)) / (1 << 10)) + 0xD800;
      var second = (unicode_code % (1 << 10)) + 0xDC00;
      utf16_codes.push(first)
      utf16_codes.push(second)
    }
  }
  return utf16_codes;
}
function convertUtf16CodesToString(utf16_codes) {
  var unescaped = '';
  for (var i = 0; i < utf16_codes.length; ++i) {
    unescaped += String.fromCharCode(utf16_codes[i]);
  }
  return unescaped;
}
function unescapeFromUtf32(str) {
  var unicode_codes = convertEscapedUtf32CodesToUnicodeCodePoints(str);
  var utf16_codes = convertUnicodeCodePointsToUtf16Codes(unicode_codes);
  return convertUtf16CodesToString(utf16_codes);
}
//END functions from http://0xcc.net/

function char2utf(char,plane){
  if (!plane){plane=15;}
  var code = hexdec(char) + 55040;
  char = dechex(plane) + dechex(code);
  var utf = '&#' + hexdec(char) + ';';
  var uni = unescapeFromUtf32("\\U000" + char.toUpperCase());
  return uni;
}

function char2unicode(char,plane){
  if (!plane){plane=15;}
  var code = hexdec(char) + 55040;
  var uni = dechex(plane) + dechex(code);
  return uni.toUpperCase();  
}

function bsw2csw(bsw, plane){
  var csw, chars;
  if (!plane){plane=15;}
  var parts = bsw.split(' ');
  var out = new Array();
  for(var i=0; i<parts.length; i++) {
    csw = '';
    chars = parts[i].chunk(3);
    for(var j=0; j<chars.length; j++) {
      csw += char2utf(chars[j],plane);
    }
    out.push(csw);
  }
  return out.join(' ');
}

function utf2char(utf){
  var val = encodeURIComponent(utf);
  var plane = val.slice(0,3);
  var a = val.slice(4,6);
  var b = val.slice(7,9);
  var c = val.slice(10,12);

  switch(plane){
    case "%F0"://plane 1
      var code = parseInt(hexdec(c)-128) + parseInt(hexdec(b)-128)*64 + parseInt(hexdec(a)-144) * 64 * 64 - 55040;
      if (code<256){
        return '0' + dechex(code);
      } else {
        return dechex(code);
      }
      break;
    case "%F3"://plane 15
      var code = parseInt(hexdec(c)-128) + parseInt(hexdec(b)-128)*64 + parseInt(hexdec(a)-176) * 64 * 64 - 55040;
      if (code<256){
        return '0' + dechex(code);
      } else {
        return dechex(code);
      }
      break;
    case "%F4"://plane 16
      var code = parseInt(hexdec(c)-128) + parseInt(hexdec(b)-128)*64 + parseInt(hexdec(a)-128) * 64 * 64 - 55040;
      if (code<256){
        return '0' + dechex(code);
      } else {
        return dechex(code);
      }
      break;
  }
}

function csw2bsw(csw){
  var char;
  var matches = csw.match(/\uDBB6[\uDC00-\uDFFF]|\uDBB7[\uDC00-\uDFF9]/g);
  if (matches){
    for(var im=0; im<matches.length; im++) {
      char = utf2char(matches[im]);
      csw = csw.replace(matches[im],char);
    }
  }
  return csw;
}

function num2utf(num){
  return char2utf(dechex(num+hexdec('800')));
}

function coord2utf(arr) {
  return num2utf(arr[0]) + num2utf(arr[1]);
}
/**
 * Kartesian SignWriting
 * Symbol keys for identity
 */
// regular expression for KSW
// Matching symbol keys in Regular Expressions.
// Loose - S\p{XDigit}{5}
// Tight - S[123]\p{XDigit}{2}[012345]\p{XDigit}*/


function cswText(text){
  var csw_sym = '\uDBB6[\uDC30-\uDEBB]\uDBB6[\uDC10-\uDC15]\uDBB6[\uDC20-\uDC2F]';
  var csw_num = '\uDBB7[\uDE00-\uDFFF]';
  var csw_coord = csw_num + csw_num;
  var csw_word = '(\uDBB6\uDC00(' + csw_sym + ')+)?\uDBB6[\uDC01-\uDC04](' + csw_coord + ')(' + csw_sym + csw_coord + ')*';
  var csw_punc = csw_sym  + csw_coord;
  var csw_pattern = '^(' + csw_word + '|' + csw_punc + ')( ' + csw_word + '| ' + csw_punc  + ')*$';
  var csw_re = new RegExp(csw_pattern,"g");
  var result = text.match(csw_re);
  if (result) {
    if (text == result[0]) {
      return true;
    }
  }
  return false;
}

/**
 * Kartesian SignWriting irregular display variants
 */
function kswRaw(text){
  var ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  var ksw_coord = 'n?[0-9]+xn?[0-9]+';
  var ksw_word = '(A(' + ksw_sym + ')+)?[BLMR](' + ksw_sym + ksw_coord + ')*';
  var ksw_punc = 'S38[7-9ab][0-5][0-9a-f]';
  var ksw_pattern = '^(' + ksw_word + '|' + ksw_punc + ')( ' + ksw_word + '| ' + ksw_punc +')*$';
  var ksw_re = new RegExp(ksw_pattern,"i");
  var result = text.match(ksw_re);
  if (result) {
    if (text == result[0]) {
      return true;
    }
  }
  return false;
}

function kswExpand(text){
  var ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  var ksw_coord = 'n?[0-9]+xn?[0-9]+';
  var ksw_pcoord = '[0-9]+x[0-9]+';
  var ksw_word = '(A(' + ksw_sym + ')+)?[BLMR](' + ksw_sym + ksw_pcoord + 'x' + ksw_coord + ')*';
  var ksw_punc = 'S38[7-9ab][0-5][0-9a-f]' + ksw_pcoord;
  var ksw_pattern = '^(' + ksw_word + '|' + ksw_punc + ')( ' + ksw_word + '| ' + ksw_punc +')*$';
  var ksw_re = new RegExp(ksw_pattern,"i");
  var result = text.match(ksw_re);
  if (result) {
    if (text == result[0]) {
      return true;
    }
  }
  return false;
}

function kswLayout(text){
  var ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  var ksw_coord = 'n?[0-9]+xn?[0-9]+';
  var ksw_pcoord = '[0-9]+x[0-9]+';
  var ksw_word = '(A(' + ksw_sym + ')+)?[BLMR](' + ksw_pcoord + ')(' + ksw_sym + ksw_coord + ')*';
  var ksw_punc = 'S38[7-9ab][0-5][0-9a-f]' + ksw_coord;
  var ksw_pattern = '^(' + ksw_word + '|' + ksw_punc + ')( ' + ksw_word + '| ' + ksw_punc +')*$';
  var ksw_re = new RegExp(ksw_pattern,"i");
  var result = text.match(ksw_re);
  if (result) {
    if (text == result[0]) {
      return true;
    }
  }
  return false;
}

function kswPanel(text){
  var ksw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  var ksw_coord = 'n?[0-9]+xn?[0-9]+';
  var ksw_pcoord = '[0-9]+x[0-9]+';
  var ksw_word = '[BLMR](' + ksw_pcoord + ')(' + ksw_sym + ksw_coord + ')*';
  var ksw_panel= 'D' + ksw_pcoord + '(_' + ksw_word + ')*';
  var ksw_pattern = '^' + ksw_panel + '(' + ksw_panel +')*$';
  var ksw_re = new RegExp(ksw_pattern,"i");
  var result = text.match(ksw_re);
  if (result) {
    if (text == result[0]) {
      return true;
    }
  }
  return false;
}

function ksw2key(ksw){
  var match;
  var keys = '';
  var matches = ksw.match(/S[123][a-f0-9]{2}[012345][a-f0-9]n?[0-9]+xn?[0-9]+/g);
  for(var i=0; i<matches.length; i++) {
    match = matches[i];
    match = match.toString();
    keys += match.slice(0,6);
  }
  return keys;
}

function ksw2seq(ksw){
  var match;
  var seq = '';
  var match = ksw.match(/A(S[123][a-f0-9]{2}[012345][a-f0-9])+/);
  if (match) {
    match = match[0].toString();
    var len = match.length;
    seq = match.slice(1,len);
  }
  return seq;
}

function ksw2cluster(ksw){
  var sym,coord,mcoord, matches, match, len, strnum;
  var cluster = new Array();
  if (!ksw) {
    cluster[0][0]="M";
    cluster[0][1]="0x0";
    return cluster;
  }
  
  var seq = 'A' + ksw2seq(ksw);
  ksw = ksw.replace(seq,'');
  if ( isPunc(ksw) ){
    match = ksw.match(/S[123][a-f0-9]{2}[012345][a-f0-9]n?[0-9]+xn?[0-9]+/);
    if (match) {
      match = match[0].toString();
      sym = match.slice(0,6);
      len = match.length;
      strnum = match.slice(6,len);
      coord = str2koord(strnum);
      mcoord = koord2str(-1*coord[0],-1*coord[1]);
      cluster[0] = ['B',mcoord];
      cluster[1] = [sym,strnum];
    }
  } else {
    match = ksw.match(/[BLMR]([0-9]+x[0-9]+)?(S[123][a-f0-9]{2}[012345][a-f0-9]n?[0-9]+xn?[0-9]+)*/i);
    mcoord = match[1];
    if (mcoord) {
      mcoord = mcoord.toString();
    } else {
      mcoord = '';
    }
    
    cluster[0] = [match[0][0],mcoord];

    matches = ksw.match(/S[123][a-f0-9]{2}[012345][a-f0-9]n?[0-9]+xn?[0-9]+/g);
  
    for(var i=0; i<matches.length; i++) {
      match = matches[i];
      match = match.toString();
      len = match.length;
      var sym = match.slice(0,6);
      strnum = match.slice(6,len);
      cluster.push(new Array(sym,strnum));
    }
  }
  return cluster;
}

function cluster2min(cluster){

  for(var i=1; i<cluster.length; i++){
    if (i==0) next;
    spatial = cluster[i];
    coord = str2koord(spatial[1]);
    if (i==1){
      xMin = coord[0];
      yMin = coord[1];
    }
    xMin = Math.min(xMin,coord[0]);
    yMin = Math.min(yMin,coord[1]);
  }
  return new Array(xMin,yMin);
}


/**
 * Other functions...
 */

function signs2sort(signs){
//setup sort and index array
  var index_seq = new Array();
  var cnt_seq=0;

//now ignore punctuation
//populate seq sorting arrays
  var first_char='';
  var iValue = 0;  //index value for base counts
  for (var i=0; i<signs.length; i++) {
    data = signs[i];
    first_char = data.slice(0,3);
    cluster = first_char + bsw2cluster(data);
    seq = bsw2seq(data);
    if (seq=="") {
      seq = cluster2seq(cluster);
    }
    if (seq){//ignore empty signs
      index_seq["0FA" + seq + cluster + i]= i;
      cnt_seq++;
    }
  }
  var keys = new Array();
  if (cnt_seq){
    //sort sequence
    for(k in index_seq) { keys.push(k); }
    keys.sort( function (a, b){return (a > b) - (a < b);} );
  }
  
  var sort_keys = new Array();
  for (var i=0; i<keys.length;i++){
    sort_keys[i]=index_seq[keys[i]];
  }
  return sort_keys;
}

function signs2index(signs){
//setup index array
  var index_base = new Array();
  var cnt_base=0;

//now ignore punctuation
//populate index with basesymbols
  var first_char='';
  var iValue = 0;  //index value for base counts
  for (var i=0; i<signs.length; i++) {
    data = signs[i];
    first_char = data.slice(0,3);
    cluster = first_char + bsw2cluster(data);
    base = bsw2base(data);
    if (base){//ignore empty signs
      index_base["0FA" + base + cluster + i]= i;
      cnt_base++;
    }
  }
  var keys = new Array();
  if (cnt_base){
    //base sequence
    for(k in index_base) { keys.push(k); }
    keys.sort( function (a, b){return (a > b) - (a < b);} );
  }
  
  var index_keys = new Array();
  for (var i=0; i<keys.length;i++){
    index_keys[i]=index_base[keys[i]];
  }
  return index_keys;
}


function cluster2seq(bsw){
  var cluster = bsw2cluster(bsw);
  var seq = '';
  if (cluster){
    var bswd = bsw2iswa(cluster);
    chunks = bswd.chunk(9);
    chunks = chunks.sort();
    bswd = chunks.toString();
    seq = bswd.replace(/,/g,'');
  }
  return seq;
}

function locationsplit(bsw,iPunc, iSign){
//needs cleaned up and simplified
  var bsw_array = new Array();
  var preLoc = '';
  var unitLoc = '';
  var postLoc = '';

  var segs = bsw2segment(bsw);
  for(i=0;i<segs.length;i++) {
    if (i<iPunc) {
      if( (i == (iPunc-1)) && (iSign==0)){
        //special case to return punc
        var units = bsw2unit(segs[i]);
        for(j=0;j<units.length;j++) {
          if ((j+1)<units.length) {
            preLoc += units[j];
          } else {
            unitLoc = units[j];
          }
        }
       } else {
        preLoc += segs[i];
      }
    } else if (i>iPunc) {
      postLoc += segs[i];
    } else {  //i == iPunc
      var units = bsw2unit(segs[i]);
      for(j=0;j<units.length;j++) {
        if ((j+1)<iSign) {
          preLoc += units[j];
        } else if ((j+1)>iSign) {
          postLoc += units[j];
        } else {  //(j+1) == iSign
          unitLoc = units[j];
        }
      }
    }
  } 
  bsw_array.push(preLoc);
  bsw_array.push(unitLoc);
  bsw_array.push(postLoc);
  return bsw_array;
}

/**
 * Formal SignWriting for regular expression searching
 */
function fswText(text){
  var fsw_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  var fsw_coord = '[0-9]{3}x[0-9]{3}';
  var fsw_word = '(A(' + fsw_sym + ')+)?[BLMR](' + fsw_coord + ')(' + fsw_sym + fsw_coord + ')*';
  var fsw_punc = 'S38[7-9ab][0-5][0-9a-f]' + fsw_coord;
  var fsw_pattern = '^(' + fsw_word + '|' + fsw_punc + ')( ' + fsw_word + '| ' + fsw_punc +')*$';
  var fsw_re = new RegExp(fsw_pattern,"i");
  var result = text.match(fsw_re);
  if (result) {
    if (text == result[0]) {
      return true;
    }
  }
  return false;
}

function fswQuery(text){
  var fsw_range = 'R[123][0-9a-f]{2}t[123][0-9a-f]{2}';
  var fsw_sym = 'S[123][0-9a-f]{2}[0-5u][0-9a-fu]';
  var fsw_coord = '([0-9]{3}x[0-9]{3})?';
  var fsw_var = '(V[0-9]+)?';
  var fsw_query = 'QT?(' + fsw_range + fsw_coord + ')*(' + fsw_sym + fsw_coord + ')*' + fsw_var;
  var fsw_pattern = '^' + fsw_query + '$';
  var fsw_re = new RegExp(fsw_pattern,"i");
  var result = text.match(fsw_re);
  if (result) {
    if (text == result[0]) {
      return true;
    }
  }
  return false;
}

function range2regex(min,max,hex,test){
  var pattern, re, diff, tmax, cnt, text, minV, maxV;
  if (!hex) hex='';
  if (!test) text='';
  min = ("000" + min).slice(-3);
  max = '' + max;
  pattern='';
//  if (val=='uuu') return '[0-9]{3}';
  //assume numbers are 3 digits long

  if (min===max) return min;

if (test) console.log( "Original values " + min);

  //ending pattern will be series of connected OR ranges
  re = [];

  //first pattern+  10's don't match and the min 1's are not zero
  //odd number to 9
  if (!(min[0]==max[0] && min[1]==max[1])) {
    if (min[2]!='0'){
      pattern = min[0] + min[1];
      if (hex) {
        //switch for dex
        switch (min[2]){
        case "f":
          pattern += 'f';
          break;
        case "e":
          pattern += '[ef]';
          break;
        case "d":
        case "c":
        case "b":
        case "a":
          pattern += '[' + min[2] + '-f]';
          break;
        default:
          switch (min[2]){
            case "9":
           pattern += '[9a-f]';
            break;
          case "8":
            pattern += '[89a-f]';
            break;
          default:
           pattern += '[' + min[2] + '-9a-f]';
            break;
          }
          break;
        }
        diff = 15-hexdec(min[2]) +1;
        min = '' + dechex((hexdec(min)+diff));
        re.push(pattern); 
      } else {
        //switch for dex
        switch (min[2]){
        case "9":
          pattern += '9';
          break;
        case "8":
          pattern += '[89]';
          break;
        default:
         pattern += '[' + min[2] + '-9]';
          break;
        }
        diff = 9-min[2] +1;
        min = '' + (min*1 + diff);
        re.push(pattern); 
      }
    }
  }
if (test) {
  console.log( "Bring up the non zero digits");
  if (pattern) {
    console.log( "* Step One: " + pattern + " for new values " + min);
  } else {
    console.log( "* Step One: NA");
  }
}
pattern = '';

  //if hundreds are different, get odd to 99 or ff
  if (min[0]!=max[0]){
    if (min[1]!='0'){
      if (hex){
        //scrape to ff
        pattern = min[0];
        switch (min[1]){
        case "f":
          pattern += 'f';
          break;
        case "e":
          pattern += '[ef]';
          break;
        case "d":
        case "c":
        case "b":
        case "a":
         pattern += '[' + min[1] + '-f]';
          break;
        case "9":
         pattern += '[9a-f]';
          break;
        case "8":
         pattern += '[89a-f]';
          break;
        default:
         pattern += '[' + min[1] + '-9a-f]';
          break;
        }
        pattern += '[0-9a-f]';
        diff = 15-hexdec(min[1]) +1;
        min = '' + dechex(hexdec(min)+diff*16);
        re.push(pattern); 
      } else {
        //scrape to 99
        pattern = min[0];
        diff = 9-min[1] +1;
        switch (min[1]){
        case "9":
          pattern += '9';
          break;
        case "8":
          pattern += '[89]';
          break;
        default:
         pattern += '[' + min[1] + '-9]';
          break;
        }
        pattern += '[0-9]';
        diff = 9-min[1] +1;
        min = '' + (min*1 + diff*10);
        re.push(pattern); 
      }
    }
  }
if (test) {
  console.log( "Bring up the 10's if hundreds are different");
  if (pattern) {
    console.log( "* Step Two: " + pattern + " for new values " + min);
  } else {
    console.log( "* Step Two: NA");
  }
}
pattern = '';

  //if hundreds are different, get to same
  if (min[0]!=max[0]){
    if (hex){
      diff = hexdec(max[0]) - hexdec(min[0]);
      tmax = dechex(hexdec(min[0]) + diff-1);
    
      switch (diff){
      case 1:
        pattern = min[0];
        break;
      case 2:
        pattern = '[' + min[0] + tmax + ']';
        break;
      default:
        if (hexdec(min[0])>9){
          minV = 'h';
        } else {
          minV = 'd';
        }
        if (hexdec(tmax)>9){
          maxV = 'h';
        } else {
          maxV = 'd';
        }
        switch (minV + maxV){
        case "dd":
          pattern += '[' + min[0] + '-' + tmax + ']';
          break;
        case "dh":
          diff = 9 - min[0];
          //firs get up to 9
          switch (diff){
          case 0:
            pattern += '[9';
            break;
          case 1:
            pattern += '[89';
            break;
          default:
            pattern += '[' + min[0] + '-9';
            break;
          }
          switch (tmax[0]){
          case 'a':
            pattern += 'a]';
            break;
          case 'b':
            pattern += 'ab]';
            break;
          default:
            pattern += 'a-' + tmax + ']';
            break;
          }
          break;
        case "hh":
          pattern += '[' + min[0] + '-' + tmax + ']';
          break;
        }
      }

      pattern += '[0-9a-f][0-9a-f]';
      diff = hexdec(max[0]) - hexdec(min[0]);
      min = '' + dechex(hexdec(min)+diff*256);
      re.push(pattern); 
    } else {
      diff = max[0] - min[0];
      tmax = min[0]*1 + diff-1;
    
      switch (diff){
      case 1:
        pattern = min[0];
        break;
      case 2:
        pattern = '[' + min[0] + tmax + ']';
        break;
      default:
       pattern = '[' + min[0] + '-' + tmax + ']';
        break;
      }
      pattern += '[0-9][0-9]';
      min = '' + (min*1 + diff*100);
      re.push(pattern); 
    }
  }
if (test) {
  console.log( "Bring up the 100's if different");
  if (pattern) {
    console.log( "* Step Three: " + pattern + " for new values " + min);
  } else {
    console.log( "* Step Three: NA");
  }
}
pattern = '';

  //if tens are different, get to same
  if (min[1]!=max[1]){
    if (hex){
      diff = hexdec(max[1]) - hexdec(min[1]);
      tmax = dechex(hexdec(min[1]) + diff-1);
      pattern = min[0];
      switch (diff){
      case 1:
        pattern += min[1];
        break;
      case 2:
        pattern += '[' + min[1] + tmax + ']';
        break;
      default:

        if (hexdec(min[1])>9){
          minV = 'h';
        } else {
          minV = 'd';
        }
        if (hexdec(tmax)>9){
          maxV = 'h';
        } else {
          maxV = 'd';
        }
        switch (minV + maxV){
        case "dd":
          pattern += '[' + min[1];
          if (diff>1) pattern += '-';
          pattern += tmax + ']';
          break;
        case "dh":
          diff = 9 - min[1];
          //firs get up to 9
          switch (diff){
          case 0:
            pattern += '[9';
            break;
          case 1:
            pattern += '[89';
            break;
          default:
            pattern += '[' + min[1] + '-9';
            break;
          }
          switch (max[1]){
          case 'a':
            pattern += ']';
            break;
          case 'b':
            pattern += 'a]';
            break;
          default:
            pattern += 'a-' + dechex(hexdec(max[1])-1) + ']';
            break;
          }
          break;
        case "hh":
          pattern += '[' + min[1];
          if (diff>1) pattern += '-';
          pattern += dechex(hexdec(max[1])-1) + ']';
          break;
        }
        break;
      }
      pattern += '[0-9a-f]';
      diff = hexdec(max[1]) - hexdec(min[1]);
      min = '' + dechex(hexdec(min)+diff*16);
      re.push(pattern); 
    } else {
      diff = max[1] - min[1];
      tmax = min[1]*1 + diff-1;
      pattern = min[0];
      switch (diff){
      case 1:
        pattern += min[1];
        break;
      case 2:
        pattern += '[' + min[1] + tmax + ']';
        break;
      default:
       pattern += '[' + min[1] + '-' + tmax + ']';
        break;
      }
      pattern += '[0-9]';
      min = '' + (min*1 + diff*10);
      re.push(pattern); 
    }

  }
if (test) {
  console.log( "Bring up the 10's");
  if (pattern) {
    console.log( "* Step Four: " + pattern + " for new values " + min);
  } else {
    console.log( "* Step Four: NA");
  }
}
pattern = '';

  //if digits are different, get to same
  if (min[2]!=max[2]){
    if (hex){
      pattern = min[0] + min[1];
      diff = hexdec(max[2]) - hexdec(min[2]);
      if (hexdec(min[2])>9){
        minV = 'h';
      } else {
        minV = 'd';
      }
      if (hexdec(max[2])>9){
        maxV = 'h';
      } else {
        maxV = 'd';
      }
      switch (minV + maxV){
      case "dd":
        pattern += '[' + min[2];
        if (diff>1) pattern += '-';
        pattern += max[2] + ']';
        break;
      case "dh":
        diff = 9 - min[2];
        //firs get up to 9
        switch (diff){
        case 0:
          pattern += '[9';
          break;
        case 1:
          pattern += '[89';
          break;
        default:
          pattern += '[' + min[2] + '-9';
          break;
        }
        switch (max[2]){
        case 'a':
          pattern += 'a]';
          break;
        case 'b':
          pattern += 'ab]';
          break;
        default:
          pattern += 'a-' + max[2] + ']';
          break;
        }
        
        break;
      case "hh":
        pattern += '[' + min[2];
        if (diff>1) pattern += '-';
        pattern += max[2] + ']';
        break;
      }
      diff = hexdec(max[2]) - hexdec(min[2]);
      min = '' + dechex(hexdec(min) + diff);
      re.push(pattern); 
    } else {
      diff = max[2] - min[2];
      pattern = min[0] + min[1];
      switch (diff){
      case 0:
        pattern += min[2];
        break;
      case 1:
        pattern += '[' + min[2] + max[2] + ']';
        break;
      default:
       pattern += '[' + min[2] + '-' + max[2] + ']';
        break;
      }
      min = '' + (min*1 + diff);
      re.push(pattern); 
    }
  }
if (test) {
  console.log( "Bring up the 1's");
  if (pattern) {
    console.log( "* Step Five: " + pattern + " for new values " + min);
  } else {
    console.log( "* Step Five: NA");
  }
}
pattern = '';



  //last place is whole hundred
  if (min[2]=='0' && max[2]=='0') {
    pattern = max;
    re.push(pattern);
  }
if (test) {
  console.log( "Match Zero endings");
  if (pattern) {
    console.log( "* Step Six: " + pattern + " for new values " + min);
  } else {
    console.log( "* Step Six: NA");
  }
}
pattern = '';
  
  cnt = re.length;
  if (cnt==1){
    pattern = re[0];
  } else {
    pattern = re.join(')|(');
    pattern = '((' + pattern + '))';
  }
  return pattern;
}

function query2regex (query,fuzz){
  var fsw_pattern, part, from, to, re_range, segment, x, y, base, fill, rotate;
  if (!fuzz) fuzz = 20;

  var re_sym = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  var re_coord = '[0-9]{3}x[0-9]{3}';
  var re_word = '[BLMR](' + re_coord + ')(' + re_sym + re_coord + ')*';
  var re_term = '(A(' + re_sym+ ')+)';

  var fsw_range = 'R[123][0-9a-f]{2}t[123][0-9a-f]{2}';
  var fsw_sym = 'S[123][0-9a-f]{2}[0-5u][0-9a-fu]';
  var fsw_coord = '([0-9]{3}x[0-9]{3})?';
  var fsw_var = '(V[0-9]+)';
  var fsw_query = 'QT(' + fsw_range + fsw_coord + ')*(' + fsw_sym + fsw_coord + ')*' + fsw_var + '?';

  if (!fswQuery(query)) return;

  if (!query || query=='Q'){
    return [re_word];
  }

  if (!query || query=='QT'){
    return [re_term + re_word];
  }

  var segments = [];

  var term = !!(query.indexOf('T')+1);

  //get the variance
  var matches = query.match(RegExp(fsw_var,'g'));
  if (matches) fuzz = matches.toString().slice(1)*1;

  //this gets all symbols with or without location
  fsw_pattern = fsw_sym + fsw_coord;
  var matches = query.match(RegExp(fsw_pattern,'g'));
  if (matches){
    for(var i=0; i<matches.length; i++) {
      part = matches[i].toString();
      base = part.slice(1,4);
      segment = 'S' + base;

      fill = part.slice(4,5);
      if (fill=='u') {
        segment += '[0-5]';
      } else {
        segment += fill;
      }
    
      rotate = part.slice(5,6);
      if (rotate=='u') {
        segment += '[0-9a-f]';
      } else {
        segment += rotate;
      }
    
      if (part.length>6){

        x = part.slice(6,9)*1;
        y = part.slice(10,13)*1;
        //now get the x segment range+++
        segment += range2regex((x-fuzz),(x+fuzz));
        segment += 'x';
        segment += range2regex((y-fuzz),(y+fuzz));
      } else {
        segment += re_coord;
      }
      //now I have the specific search symbol
      // add to general ksw word
      segment = re_word + segment + '(' + re_sym + re_coord + ')*';
      if (term) segment = re_term + segment;
      segments.push(segment);
    }
  }

  //this gets all ranges
  fsw_pattern = fsw_range + fsw_coord;
  var matches = query.match(RegExp(fsw_pattern,'g'));
  if (matches){
    for(var i=0; i<matches.length; i++) {
      part = matches[i].toString();
      from = part.slice(1,4);
      to = part.slice(5,8);
      re_range = range2regex(from,to,"hex");
      segment = 'S' + re_range + '[0-5][0-9a-f]';
      if (part.length>8){

        x = part.slice(8,11)*1;
        y = part.slice(12,15)*1;
        //now get the x segment range+++
        segment += range2regex((x-fuzz),(x+fuzz));
        segment += 'x';
        segment += range2regex((y-fuzz),(y+fuzz));
      } else {
        segment += re_coord;
      }
      // add to general ksw word
      segment = re_word + segment + '(' + re_sym + re_coord + ')*';
      if (term) segment = re_term + segment;
      segments.push(segment);
    }
  }
  
  return segments;
}

function query_displace(qsearch,x,y){
  var str, str_new, coord;
  var fsw_coord = '[0-9]{3}x[0-9]{3}';

  //this gets all symbols with or without location
  var matches = qsearch.match(RegExp(fsw_coord,'g'));
  if (matches){
    for(var i=0; i<matches.length; i++) {
      str = matches[i].toString();
      coord = str2coord(str);
      coord[0] += x;
      coord[1] += y;
      str_new = coord2str(coord[0],coord[1]);
      qsearch = qsearch.replace(str,str_new);
    }
  }
  return qsearch;
}

function query2displace(qsearch){
  var fuzz=20;
  if (!fswQuery(qsearch)) return;

  var fsw_var = '(V[0-9]+)';

  var matches = qsearch.match(RegExp(fsw_var,'g'));
  if (matches) fuzz = matches.toString().slice(1)*1;

  var qsa = [];
  qsa.push(query_displace(qsearch,fuzz*-2,fuzz*-2));
  qsa.push(query_displace(qsearch,0,fuzz*-2));
  qsa.push(query_displace(qsearch,fuzz*2,fuzz*-2));
  qsa.push(query_displace(qsearch,fuzz*-2,0));
  qsa.push(query_displace(qsearch,fuzz*2,0));
  qsa.push(query_displace(qsearch,fuzz*-2,fuzz*2));
  qsa.push(query_displace(qsearch,0,fuzz*2));
  qsa.push(query_displace(qsearch,fuzz*2,fuzz*2));
  return qsa;
}

function query_results(qsearch,input){
var pattern, matches, parts;
  //return array of words
  
  var re = query2regex(qsearch);
  
  for(var i=0; i<re.length; i++) {
    pattern = re[i];
    matches = input.match(RegExp(pattern,'g'));
    input = matches.join(' ');
    //normalize to M or B ?
    input = input.replace('L','M');
    input = input.replace('R','M');
    input = input.replace('B','M');
  }

  if (input){
    parts = input.split(' ');
    var u = {}, words = [];
    for(var i = 0, l = parts.length; i < l; ++i){
      if(u.hasOwnProperty(parts[i])) {
        continue;
      }
      words.push(parts[i]);
      u[parts[i]] = 1;
    }
  } else {
    words = [];
  }

  return words;
}

function query_counts(qsearch,input){
  //return counts[0] array of words
  //return counts[1] array of word counts
  //return counts[2] value of grand total
  var cnt, i, j, k, u, re, pattern, matches, match, words;
  
  re = query2regex(qsearch);
  for(i=0; i<re.length; i++) {
    pattern = re[i];
    matches = input.match(RegExp(pattern,'g'));
    if (matches) {
      input = matches.join(' ');
      // this gets word counts for the first match only match!
      // following searches are subset
      if (!cnt){
        cnt = {};
        for(j=0; j<matches.length; j++) {
          match = matches[j];
          match = 'M' + match.slice(1);
          if (cnt[match]){
            cnt[match]++;
          } else {
            cnt[match]=1;;
          }
        }
      }
    }
    
    u = {};
    words = [];
    if (matches){
      for(k = 0; k < matches.length; ++k){
        match = matches[k];
        match = 'M' + match.slice(1);
        if(u.hasOwnProperty(match)) {
          continue;
        }
        words.push(match);
        u[words[k]] = 1;
      }
      input = words.join(' ');
      //normalize to M or B ?
      input = input.replace('L','M');
      input = input.replace('R','M');
      input = input.replace('B','M');
    } else {
      input = '';
    }
  }

  if (input){
    words = input.split(' ');
  } else {
    words = [];
  }
  //display signs
  var wcount = words.length;
  if (input) wcount=0;

  var gtot = 0;
  for(i=0; i<words.length; i++) {
    gtot += cnt[words[i]];
  }

  var counts = [];
  counts.push(words);
  counts.push(cnt);
  counts.push(gtot);
  return counts;
}

/**
 * Modern SignWriting script encoding library
 */
function isVert (text){
  var lp = !!(text.indexOf("L")+1);
  var mp = !!(text.indexOf("M")+1);
  var rp = !!(text.indexOf("R")+1);
  return lp!==false || mp!==false || rp!==false;
}

function ksw2fsw(ksw){
  var matches, segment, input, output, j, spatial, len, first, key, klen, str, coord;
  var pattern = '([BLMR]|S[123][0-9a-f]{2}[0-5][0-9a-f])n?[0-9]+xn?[0-9]+';
  var segments = ksw.split(' ');
  var segsout = [];
  for(var i=0; i<segments.length; i++) {
    segment = segments[i];
    matches = segment.match(RegExp(pattern,'g'));
    input = '';
    output = '';
    for(j=0; j<matches.length; j++) {
      spatial = matches[j];
      len = spatial.legnth;
      first = spatial[0];
      if (first=='S'){
        key = spatial.slice(0,6);
      } else {
        key = first;
      }
      klen = key.length;
      str = spatial.slice(klen);
      coord = str2koord(str);
      input += spatial;
      output += key + (coord[0]+500) + 'x' + (coord[1]+500);
    }
    segment = segment.replace(input,output);
    segsout.push(segment);
  }
  return segsout.join(' ');
}

function fsw2ksw(fsw){
  var segment, matches, input, output, j, spatial, len, first, key, klen, str, coord;
  var segments = fsw.split(' ');
  var segsout = []
  var pattern = '([BLMR]|S[123][0-9a-f]{2}[0-5][0-9a-f])[0-9]{3}x[0-9]{3}';
  var segsout = [];
  for(var i=0; i<segments.length; i++) {
    segment = segments[i];
    matches = segment.match(RegExp(pattern,'g'));
    input = '';
    output = '';
    for(j=0; j<matches.length; j++) {
      spatial = matches[j];
      len = spatial.legnth;
      first = spatial[0];
      if (first=='S'){
        key = spatial.slice(0,6);
      } else {
        key = first;
      }
      klen = key.length;
      str = spatial.slice(klen);
      coord = str2koord(str);
      input += spatial;
      output += key + koord2str(coord[0]-500, coord[1]-500);
    }
    segment = segment.replace(input,output);
    segsout.push(segment);

  }
  return segsout.join(' ');
}

function bsw2fsw(bsw){
  var words, pattern, matches, bword, fsw, chars, coord, j, char;
  words = [];
  //match each bsw word+++
  pattern = '([1-8][0-9a-f]{2})+';

  matches = bsw.match(RegExp(pattern,'g'));
  for(var i=0; i<matches.length; i++) {
    bword = matches[i];
    fsw = '';
    //match each bsw char+++
    pattern = '([1-8][0-9a-f]{2})';


    chars = bword.match(RegExp(pattern,'g'));
    coord = '';
    for(j=0; j<chars.length; j++) {
      char = chars[j];
      if (inHexRange('100','104',char)){
        switch(char){
          case '100':
            fsw += "A";
            break;
          case '101':
            fsw += "B";
            break;
          case '102':
            fsw += "L";
            break;
          case '103':
            fsw += "M";
            break;
          case '104':
            fsw += "R";
            break;
        }
      } else if (inHexRange('110','115',char)){
        fsw += char.slice(2,3);
      } else if (inHexRange('120','12f',char)){
        fsw += char.slice(2,3);
      } else if (inHexRange('130','3bb',char)){
        fsw += 'S' + dechex(hexdec(char) - hexdec('30'));
      } else if (inHexRange('706','8f9',char)){
        if (coord){
          coord += char;
          coord = bsw2coord(coord);
          fsw += (coord[0]+500) + 'x' + (coord[1]+500);
          coord = '';
        } else {
          coord = char;
        }
      }      
    }
    words.push(fsw);
  }

  fsw = words.join(' ');
  return fsw;
}

function fsw2bsw(fsw){
  var str, coord, key;
  var pattern = '[0-9]{3}x[0-9]{3}';

  var matches = fsw.match(RegExp(pattern,'g'));
  for(var i=0; i<matches.length; i++) {
    str = matches[i];
    coord = str2koord(str);
    coord[0] -= 500;
    coord[1] -= 500;
    fsw = fsw.replace(str,num2bsw(coord[0]) + num2bsw(coord[1]));
  }

  pattern = 'S[123][0-9a-f]{2}[0-5][0-9a-f]';
  matches = fsw.match(RegExp(pattern,'g'));
  for(i=0; i<matches.length; i++) {
    key = matches[i];
    fsw = fsw.replace(key,key2bsw(key));
  }

  fsw = fsw.replace('A','100');
  fsw = fsw.replace('B','101');
  fsw = fsw.replace('L','102');
  fsw = fsw.replace('M','103');
  fsw = fsw.replace('R','104');

  return fsw;
}