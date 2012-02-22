/**
 * Formal SignWriting Library for JavaScript
 * 
 * Copyright 2007-2011 Stephen E Slevinski Jr
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
 * @copyright 2007-2011 Stephen E Slevinski Jr 
 * @author Steve (slevin@signpuddle.net)  
 * @license http://www.opensource.org/licenses/gpl-3.0.html GPL
 * @access public
 * @version 2.0
 * @filesource
 *   
 */
sym_write = 'S10000';

/**
 * Binary SignWriting for plain text symbol encoding
 *   bsw = 3 digit strings of 3
 *   key = 6 digit string starting with S, 5 without
 *   uni = Unicode string of 3 plane 1 characters
 * 
 */
//sized chunks
String.prototype.chunk = function(n) {
  if (typeof n=='undefined') n=2;
  return this.match(RegExp('.{1,'+n+'}','g'));
};

//base section
function bsw2base(bsw){
  if (bsw=="")return;
  var bsw_base = '';
  var chunks = bsw.chunk(3);
  chunks = chunks.sort();
  forEach(chunks,function(char){
    if(isISWA(char)){
      bsw_base += char;
    }
  });
  return bsw_base
}

function base2view(base){
  var view = base + '00';
  if (isHand(base)){
    if(!isSymGrp(base)){
      view = base + '10';
    }
  }
  return view;
}
//group section
var sg_list = new Array('100','10e','11e','144','14c','186','1a4','1ba','1cd','1f5', '205','216','22a','255','265','288','2a6','2b7','2d5','2e3', '2f7', '2ff','30a','32a','33b','359', '36d','376', '37f', '387');
var sg_colorize = new Array('0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', '0000CC', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'CC0000', 'FF0099', '006600', '006600', '006600', '006600', '006600', '000000', '000000', '884411', 'FF9900');
function base2group(base){
var sg_list = new Array('100','10e','11e','144','14c','186','1a4','1ba','1cd','1f5', '205','216','22a','255','265','288','2a6','2b7','2d5','2e3', '2f7', '2ff','30a','32a','33b','359', '36d','376', '37f', '387');
  for(i=0;i<sg_list.length;i++) {
    group = sg_list[i];
    if (hexdec(base)==hexdec(group)) return group;
    if (hexdec(base)<hexdec(group)) return prev;
    prev = group;
  }
  return group;
}

//function isSymGrp(char){
//  var symgrp = findValue(sg_list,char);
//  if (symgrp == -1) {
//    return false;
//  } else {
//    return true;
//  } 
//}

//cat section
var cat_list = new Array('100','205','2f7', '2ff','36d','37f','387');
function base2cat(base){
  var cat_list = new Array('100','205','2f7', '2ff','36d','37f','387');
  for(i=0;i<cat_list.length;i++) {
    cat = cat_list[i];
    if (hexdec(base)==hexdec(cat)) return cat;
    if (hexdec(base)<hexdec(cat)) return prev;
    prev = cat;
  }
  return cat;
}

function dechex(d) {return d.toString(16);}

function hexdec(h) {return parseInt(h,16);} 

//symbol section
function key2bsw(key){
  var base = key.slice(0,3);
  var fill = key.slice(3,4);
  var rot = key.slice(4,5);
  return base + fill2char(fill) + rot2char(rot);
}

function bsw2key(bsw){
  var base = bsw.slice(0,3);
  var fill = bsw.slice(3,6);
  var rot = bsw.slice(6,9);
  return base + char2fill(fill) + char2rot(rot);
}

function bsw2code(sym){
  return ((hexdec(sym.slice(0,3)) - 256) * 96) + ((hexdec(sym.slice(3,6))-908)*16) + hexdec(sym.slice(6,9))-913;
}

function code2bsw(code){
  base = parseInt(code/96);
  code = code - base*96;
  fill = parseInt(code/16);
  rot = code - (fill*16);
  return dechex(base+256) + dechex((fill-1)+909) + dechex(rot+913);
}

function char2fill(char){
  return dechex(hexdec(char)-908);
}

function fill2char(fill){
  return dechex(hexdec(fill)+908);
}

function char2rot(char){
  return dechex(hexdec(char)-914);
}

function rot2char(rot){
  return dechex(hexdec(rot)+914);
}

inHexRange = function(start,end,char){
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
isLoq = function (char) {return inHexRange("37f","386",char);}
isPunc = function (char) {return inHexRange("387","38b",char);} 
isFill = function (char) {return inHexRange("38c","391",char); }
isRot = function (char) {return inHexRange("392","3a1",char); }


/**
 * Number section
 */
function koord2str(x,y){
  str = '';
  if (x<0) str += 'n';
  str += Math.abs(x);
  str += 'x';
  if (y<0) str += 'n';
  str += Math.abs(y);
  return str;
}

function str2koord(str) {
  str = str.replace(/n/gi,'-');
  parts = str.split('x');
  x = parseInt(parts[0]);
  y = parseInt(parts[1]);
  coord = [x,y];
  return coord;
} 

/**
 * Cartesian SignWriting rich text script encoding
 *   ksw = lite markup with 6 digit string starting with S for symbols
 *   csw = lite markup with proposed Unicode string of 3 plane 1 characters for symbols
 *    
 */
 //Text Strings in KSW
hello = "S10001";
hello_seq = "S10001";
world = "S10f10n4xn1";
world_seq = "S10f10";
period = "";
hello_world = hello + world + period;


/**
 * Kartesian SignWriting
 * Symbol keys for identity
 */
// regular expression for KSW
// Matching symbol keys in Regular Expressions.
// Loose - S\p{XDigit}{5}
// Tight - S[123]\p{XDigit}{2}[012345]\p{XDigit}*/


/* 
 * regular expression for CSW
(A([\x{1D800}-\x{1DA8B}][\x{1DA8C}-\x{1DA91}][\x{1DA92}-\x{1DAA1}])+)?[LMR](n?\p{Digit}+xn?\p{Digit}+)?([\x{1D800}-\x{1DA8B}][\x{1DA8C}-\x{1DA91}][\x{1DA92}-\x{1DAA1}]n?\p{Digit}+xn?\p{Digit}+)*
S\p{XDigit}{5}
*/

function validCSW(csw){
  ksw = csw2ksw(csw);
  return validKSW(ksw);
}

function validKSW(ksw){
  return /^(A(S[123][a-f0-9]{2}[012345][a-f0-9])+)?[LMR](n?[0-9]+xn?[0-9]+)?(S[123][a-f0-9]{2}[012345][a-f0-9]n?[0-9]+xn?[0-9]+)*$/i.test(ksw);
}

// reg exp scrapers
function ksw2cluster(ksw){
  var max_match = ksw.match(/[LMR]n?[0-9]+xn?[0-9]+/i);
  var len = max_match[0].length;
  var strnum = max_match[0].slice(1,len);
  coord = str2koord(strnum);
  var cluster = new Array();
  cluster.push(coord);

  matches = ksw.match(/S[123][a-f0-9]{2}[012345][a-f0-9]n?[0-9]+xn?[0-9]+/g);
  
  for(i=0; i<matches.length; i++) {
    match = matches[i];
    match = match.toString();
    len = match.length;
    var sym = match.slice(0,6);
    strnum = match.slice(6,len);
    cluster.push(new Array(sym,strnum));
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
 * Unicode Proposed Integration for symbols
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
  if (!plane){plane=1;}
  code = hexdec(char) + 55040;
  char = dechex(plane) + dechex(code);
  var utf = '&#' + hexdec(char) + ';';
  var uni = unescapeFromUtf32("\\U000" + char.toUpperCase());
  return uni;
}

function bsw2utf(bsw,plane){
  if (!plane){plane=1;}
  var bsw_utf = '';
  var chunks = bsw.chunk(3);
  for(i=0; i<chunks.length; i++) bsw_utf += char2utf(chunks[i],plane);
  return bsw_utf;
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

function utf2bsw(bsw_utf){
  var bsw = '';
  var chunks = bsw_utf.chunk(2);
  for(i=0; i<chunks.length; i++) bsw += utf2char(chunks[i]);
  return bsw;
}

/**
 * Unicode Integration lite markup
 */
function csw2ksw(csw){
  var matches = csw.match(/\uD836[\uDC00-\uDE8B]\uD836[\uDE8C-\uDE91]\uD836[\uDE92-\uDEA1]/g);
  if (matches){
    for(im=0; im<matches.length; im++) {
      key = 'S' + bsw2key(utf2bsw(matches[im]));
      csw = csw.replace(matches[im],key);
    }
  }
  return csw;//really ksw
}

function ksw2csw(ksw){
  var bsw;
  matches = ksw.match(/S[123][a-f0-9]{2}[012345][a-f0-9]/g);
  for(i=0; i<matches.length; i++) {
    bsw = key2bsw(matches[i].slice(1,6));
    ksw = ksw.replace(matches[i],bsw2utf(bsw));
  }
  return ksw;//really csw
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
