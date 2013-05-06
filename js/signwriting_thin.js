/**
 * Universal SignWriting Plugin, thin viewer
 *
 * Installation
 *   add this file to any web site or add these 2 lines to any web page
 
 * Copyright 2007-2013 Stephen E Slevinski Jr
 * Steve (Slevin@signpuddle.net)
 *
 * This file is part of SWIS: the SignWriting Icon Server.
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
 * @version 1.0.0.rc
 * @filesource
 *
 */

/**
 * Line 1
 *   first, define a function for regular expression search and replace
 *   second, crawl the document object model for TEXT elements with matching strings and apply the function
 */
signwriting_thin=(function(node){var u='http://swis.wmflabs.org/',v='1.0.0-rc',s1,s2,d,p,r,r2,o,f;r=/[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*|S38[7-9ab][0-5][0-9a-f][0-9]{3}x[0-9]{3}/g;r2=/[0-9]{3}x[0-9]{3}/g;o={};o.L=-1;o.R=1;f=function(m){var x,x1=500,x2=500,y,y1=500,y2=500,k,w,h,l;k=m.charAt(0);m.replace(r2,function($0){x=parseInt($0.slice(0,3));y=parseInt($0.slice(4,7));x1=Math.min(x1,x);x2=Math.max(x2,x);y1=Math.min(y1,y);y2=Math.max(y2,y);});if(k=='S'){x2=1000-x1;y2=1000-y1;}w=x2-x1;h=y2-y1;l=o[k]||0;l=l*75+x1-400;return '<div style="padding:10px;position:relative;background-repeat:no-repeat;background-origin:content-box;width:'+w+'px;height:'+h+'px;left:'+l+'px;background-image:url(\''+u+'glyphogram.php?font=svg&text='+m+'\');"><span style="display:table-cell;vertical-align:middle;font-size:0%;height:inherit;">'+m+' </span></div>';};function fswReplace(node){if(node.nodeType==3){s1=node.nodeValue;s2=s1.replace(r,f);if(s1!=s2){p=node.parentNode;d=document.createElement('div');d.innerHTML=s2;p.replaceChild(d,node);}}else{var nodes;if(node.nodeName!='TEXTAREA')nodes=node.childNodes;if(nodes){var i=nodes.length;while(i--)fswReplace(nodes[i]);}}};if(!node||!node.nodeType)node=document.body;fswReplace(node);});

/**
 * Line 2
 *   execute function when DOM is loaded
 */
window.addEventListener ? window.addEventListener("load",signwriting_thin,false) : window.attachEvent && window.attachEvent("onload",signwriting_thin);
