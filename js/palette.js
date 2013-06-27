/**
 * Symbol Palette javascript
 * 
 * Copyright 2007-2013 Stephen E Slevinski Jr
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
 * @copyright 2007-2013 Stephen E Slevinski Jr 
 * @author Steve (slevin@signpuddle.net)  
 * @license http://www.opensource.org/licenses/gpl-3.0.html GPL
 * @access public
 * @package SWIS
 * @version 2.0
 * @filesource
 *   
 */

function loadPalette(){
  console.log("here!");
  Palette = new Array();
  setupPalette();
//  DoResize();
//  floatPalette();
}

/**
 * general ISWA functions
 */
function dechex(d) {return d.toString(16);}
function hexdec(h) {return parseInt(h,16);}

function validkey(code){
//  if (code<256) {return 0;}
  base = code.slice(0,3);
  for (var j=0;j<keyISWA.length;j++){
    for (var k=0;k<keyISWA[j].length;k++){
      if (keyISWA[j][k][0]==base) {
        tGrp=j;
        tSym=k;
        break;
      } 
    } 
  }
  if (code.length==3){
    return 1;
  } else {
    RotKey = keyISWA[tGrp][tSym][5];
    FilKey = keyISWA[tGrp][tSym][4];

    sfill = hexdec(code.slice(3,4));
    srot = hexdec(code.slice(4,5));

    fPow = Math.pow(2,sfill);
    rPow = Math.pow(2,srot);

    if ((RotKey & rPow) && (FilKey & fPow)){
      return 1;
    } else { 
      return 0;
    }
  }
}

/**
 * General Palette Functions
 */
function setupPalette(){
  var dPalette=$('#palette');
  var key = 0;
  var ihtml = '';
  for (var row=0; row<10;row++){
    for (var col=0;col<6;col++){ 
      key = row + (col*16);
      id = 'PS' + row + '-' + col;
      ihtml += '<div class="PaletteSymbol" id="' + id + '">';
      ihtml += '<table border=1 width=100% height=100%><tr><td align=middle valign=middle>' + row + ',' + col + '</td></tr></table>';
      ihtml +='</div>';
    }
    ihtml += '<br clear="all">';
  }
  dPalette.html(ihtml);

  var id = '';
  for (var row=0; row<10;row++){
    for (var col=0;col<6;col++){ 
      key = row + (col*16);
      id = 'PS' + row + '-' + col;
      var pally =  new PaletteSymbol(row,col);
      Palette[id] = pally;
//      connect(id, "onclick", pally, "wasClicked");
//      var x = new Draggable(id,{
//        starteffect: function (element) { Palette[element.id].DragStart();}       
//        ,endeffect: function (element) { Palette[element.id].DragStop();}      
//      });
    }
  }
}

function DoResize(){
  psSize = elementDimensions("PS0-0");
  if (psSize.w < 40 || psSize.h<40) {
    if (psSize.w < 25 || psSize.h<25) {
      if (Palette["PS0-0"].size != .5) {
        PaletteSetSize(.5); 
      } 
    } else {
      if (Palette["PS0-0"].size != .7) {
        PaletteSetSize(.7); 
      } 
    }
  } else {
    if (Palette["PS0-0"].size != 1) {
      PaletteSetSize(1); 
    }
  }
}

function floatPalette(){
  var pos = getViewportPosition();
  var dPalette=$('palette');
  pageY = pos.y;
  pos = getElementPosition(dPalette);
  pos.x = null;
  var ideal = pageY+10; 
  pos.y = pos.y + (ideal-pos.y)/4;
  setElementPosition(dPalette,pos);
  setTimeout('floatPalette()',25);
  DoResize();
}

/**
 * PaletteSymbol Class
 */
function PaletteSymbol(row,col){
  this.id = "PS" + row + "-" + col;
  this.row = row;
  this.col = col;
  this.grp=0; //SymbolGroup
  this.sym=0; //BaseSymbol
  this.dtl=0; //lowest detail level
  this.code=""; 
  this.size=1; 
  this.htmlPRE = "<table border=1 height=100% width=100%><tr><td align=middle valign=middle>";
  this.htmlPREDragging = "<table><tr><td align=left valign=top>";
  this.htmlPOST = "</td></tr></table>";
  this.Refresh = fnRefresh;
  this.Dragging = fnDragging;
  this.SetTop = fnSetTop;
  this.wasClicked = fnClicked;
  this.SetGroup = fnSetGroup;
  this.SetSymbol = fnSetSymbol;
  this.DragStart = fnDragStart;
  this.DragStop = fnDragStop;
  this.SetSize = fnSetSize;
  this.SetTop();
}

  function fnDragStart(){
    addElementClass(this.id, "allvisible");
    this.Dragging();
    this.xy = elementPosition($(this.id));
    this.dragging=1;
  } 

  function fnDragStop(){
    setElementPosition($(this.id),{'x':0,'y':0});
    var pos = elementPosition(this.id);//stops flicker box
    removeElementClass(this.id, "allvisible");
    this.Refresh();
    this.dragging=0;
  } 

  function fnClicked(e){
    var src=e.src();
    if (this.dragging){
      this.dragging=0;
    } else {
      pally = Palette[src.id];
      if (pally.code) {
        if (pally.sym==0) {
          PaletteSetGroup(pally.grp) 
        }
        else if ((pally.dtl==0)) {
          PaletteSetSymbol(pally.grp,pally.sym) 
        }
      }
    }
  }
          
  function fnRefresh(){
    htmlPS = "";
    if (this.code) {
      htmlPS = '<img src="glyph.php?key=' + this.code + '&size=' + this.size + '">';
    }
    document.getElementById(this.id).innerHTML = this.htmlPRE + htmlPS + this.htmlPOST;
  }

  function fnDragging(){
    htmlPS = "";
    if (this.code) {
      htmlPS = '<img src="glyph.php?key=' + this.code +  '">';
    }    
    document.getElementById(this.id).innerHTML = htmlPS;
  }


  function fnSetSize(size){
    this.size=size;
    this.Refresh();
  }


  function fnSetTop(){
    this.grp=0;
    this.sym=0;
    this.dtl=0;
    this.code="";
    id = 1 + this.col + (this.row*16);
    idPS = 1 + this.row + (this.col*10);
    if(keyISWA.length>=idPS){
      if ((this.row<10) && (this.col<5)) {
        this.grp=idPS;
        this.sym=0;
        this.code=keyISWA[idPS-1][0][1]
      }
    }
    this.Refresh();
  }

  function fnSetGroup(selected){
    display = keyISWA[selected-1].length
    //shortcut if there is only one alternative 
    if (display==1) {
      this.SetSymbol(selected,1);
    } else {
      inCol=10;
      id = 1 + this.row + (this.col*16);
      idPS = 1 + this.row + (this.col*inCol);
      this.grp=0;
      this.sym=0;
      this.dtl=0;
      this.code="";
      if ((this.row<inCol) && (idPS<=display)) {
        this.grp=selected;
        this.sym=idPS
        if ((selected<11) && (idPS>1)){
          this.code=keyISWA[selected-1][idPS-1][1]
        } else {
          this.code=keyISWA[selected-1][idPS-1][1]
        }
      }
      this.Refresh()  
    }
  }

  function fnSetSymbol(grp,selected){
    id = this.row + (this.col*16);
//    nCode = keyISWA[grp-1][selected-1][0] + id;
    nCode = keyISWA[grp-1][selected-1][0] + dechex(this.col) + dechex(this.row);
    if (validkey(nCode)){
      this.grp=grp;
      this.sym=selected;
      this.dtl=1;
      this.code=nCode;
    } else {
      this.grp=0;
      this.sym=0;
      this.dtl=0;
      this.code="";
    }
    this.Refresh();
  }
/**
 * END PaletteSymbol Class
 */

/**
 * General PaletteSymbol Functions
 */
function PaletteSetGroup(selected){
  for (var row=0; row<16; row++) {
    for (var col=0; col<6; col++) {
      Palette['PS' + row + '-' + col].SetGroup(selected);
    }
  }
}

function PaletteSetSymbol(grp,selected){
  for (var row=0; row<16; row++) {
    for (var col=0; col<6; col++) {
      Palette['PS' + row + '-' + col].SetSymbol(grp,selected);
    }
  }
}

function PaletteSetSize(size){
  for (var row=0; row<16; row++) {
    for (var col=0; col<6; col++) {
      Palette['PS' + row + '-' + col].SetSize(size);
    }
  }
}

function PaletteSetTop(){
  for (var row=0; row<16; row++) {
    for (var col=0; col<6; col++) {
      Palette['PS' + row + '-' + col].SetTop();
    }
  }
}

function PalettePrevious(){
  var pally = Palette["PS0-0"];
  if (pally.dtl){
    PaletteSetGroup(pally.grp);
  } else {
    PaletteSetTop();
  }
}

//loadPalette();
