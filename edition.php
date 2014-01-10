<?php
/**
 *
 * Edition Section
 *
 */
 $swis_edition = @$_ENV['swis_edition'];
 if (!$swis_edition) $swis_edition = "Github Edition";
 
 $swis_url = @$_ENV['swis_url'];
 if (!$swis_url) $swis_url = curPage();
 
 $semver = "1.2.0-pre.3";
 $ed_date = "January 10th, 2014";
 


function curPage() {
  $pageURL = 'http';
  if (@$_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["SCRIPT_NAME"];
  } else {
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];
  }
  $pageURL = str_replace ('/js/','/',$pageURL);
  return str_replace (basename($_SERVER["PHP_SELF"]),'',$pageURL);
}

$thin_base = <<<EOT
(function(node){var%20u='$swis_url',v='1.0.0-rc.2',s1,s2,d,p,r,r2,o,f;r=/(A(S[123][0-9a-f]{2}[0-5][0-9a-f])+)?[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*|S38[7-9ab][0-5][0-9a-f][0-9]{3}x[0-9]{3}/g;r2=/[0-9]{3}x[0-9]{3}/g;o={};o.L=-1;o.R=1;f=function(m){var%20x,x1=500,x2=500,y,y1=500,y2=500,k,w,h,l;k=m.charAt(0);m.replace(r2,function($0){x=parseInt($0.slice(0,3));y=parseInt($0.slice(4,7));x1=Math.min(x1,x);x2=Math.max(x2,x);y1=Math.min(y1,y);y2=Math.max(y2,y);});if(k=='S'){x2=1000-x1;y2=1000-y1;}w=x2-x1;h=y2-y1;l=o[k]||0;l=l*75+x1-400;return%20'<div%20style="padding:10px;position:relative;background-repeat:no-repeat;background-origin:content-box;width:'+w+'px;height:'+h+'px;left:'+l+'px;background-image:url(\''+u+'glyphogram.php?font=svg&text='+m+'\');"><span%20style="display:table-cell;vertical-align:middle;font-size:0%;height:inherit;">'+m+'%20</span></div>';};function%20fswReplace(node){if(node.nodeType==3){s1=node.nodeValue;s2=s1.replace(r,f);if(s1!=s2){p=node.parentNode;d=document.createElement('div');d.innerHTML=s2;p.replaceChild(d,node);}}else{var%20nodes;if(node.nodeName!='TEXTAREA')nodes=node.childNodes;if(nodes){var%20i=nodes.length;while(i--)fswReplace(nodes[i]);}}};if(!node||!node.nodeType)node=document.body;fswReplace(node);})
EOT;

$thin_bookmark = "javascript:" . $thin_base . "();";

$thin_script = "signwriting_thin=" . $thin_base . ";";
$thin_script = str_replace("%20"," ",$thin_script);
$thin_start = 'window.addEventListener ? window.addEventListener("load",signwriting_thin,false) : window.attachEvent && window.attachEvent("onload",signwriting_thin);';


$styled_base = <<<EOT
(function(node){var%20u='$swis_url',v='1.0.0-rc.2',s1,s2,d,p,r,r2,o,f;r=/(A(S[123][0-9a-f]{2}[0-5][0-9a-f])+)?[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*|S38[7-9ab][0-5][0-9a-f][0-9]{3}x[0-9]{3}/g;r2=/[0-9]{3}x[0-9]{3}/g;o={};o.L=-1;o.R=1;function%20rgbToHex(rgb){if(rgb.match(/^[0-9A-Fa-f]{6}$/)){return%20rgb;}var%20rgbvals=/rgba?\((.+),(.+),(.+)\)/i.exec(rgb);if(!rgbvals){return%20'ffffff';}var%20rval=parseInt(rgbvals[1]);var%20gval=parseInt(rgbvals[2]);var%20bval=parseInt(rgbvals[3]);var%20pad=function(value){return%20(value.length<2?'0':'')+value;};return%20pad(rval.toString(16))+pad(gval.toString(16))+pad(bval.toString(16));}var%20color,background,size;f=function(m){var%20x,x1=500,x2=500,y,y1=500,y2=500,k,w,h,l;k=m.charAt(0);m.replace(r2,function($0){x=parseInt($0.slice(0,3));y=parseInt($0.slice(4,7));x1=Math.min(x1,x);x2=Math.max(x2,x);y1=Math.min(y1,y);y2=Math.max(y2,y);});if(k=='S'){x2=1000-x1;y2=1000-y1;}w=(x2-x1)*size;h=(y2-y1)*size;l=o[k]||0;l=(l*75+x1-400)*size;return%20'<div%20style="padding:10px;position:relative;background-repeat:no-repeat;background-origin:content-box;width:'+w+'px;height:'+h+'px;left:'+l+'px;background-image:url(\''+u+'glyphogram.php?font=svg&text='+m+'&line='+color+'&fill='+background+'&size='+size+'\');"><span%20style="display:table-cell;vertical-align:middle;font-size:0%;height:inherit;">'+m+'</span></div>';};function%20fswReplace(node){if(node.nodeType==3){color=rgbToHex($(node.parentNode).css('color'));background=$(node.parentNode).css('background-color');parent=node.parentNode;while(background.toString()=='rgba(0,%200,%200,%200)'||background.toString()=='transparent'){parent=parent.parentNode;background=$(parent).css('background-color');}background=rgbToHex(background);size=parseInt($(node.parentNode).css('font-size'))/20;s1=node.nodeValue;s2=s1.replace(r,f);if(s1!=s2){p=node.parentNode;d=document.createElement('div');d.innerHTML=s2;p.replaceChild(d,node);}}else{var%20nodes;if(node.nodeName!='TEXTAREA')nodes=node.childNodes;if(nodes){var%20i=nodes.length;while(i--)fswReplace(nodes[i]);}}};if(!node||!node.nodeType)node=document.body;fswReplace(node);})
EOT;

$styled_base = <<<EOT
(function (node) {
    var u = '$swis_url',
        v = '1.0.0-rc.2',
        s1, s2, d, p, r, r2, o, f;
    r = /(A(S[123][0-9a-f]{2}[0-5][0-9a-f])+)?[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*|S38[7-9ab][0-5][0-9a-f][0-9]{3}x[0-9]{3}/g;
    r2 = /[0-9]{3}x[0-9]{3}/g;
    o = {};
    o.L = -1;
    o.R = 1;

    function rgbToHex(rgb) {
        if (rgb.match(/^[0-9A-Fa-f]{6}$/)) {
            return rgb;
        }
        var rgbvals = /rgba?\((.+),(.+),(.+)\)/i.exec(rgb);
        if (!rgbvals) {
            return 'ffffff';
        }
        var rval = parseInt(rgbvals[1]);
        var gval = parseInt(rgbvals[2]);
        var bval = parseInt(rgbvals[3]);
        var pad = function (value) {
            return (value.length < 2 ? '0' : '') + value;
        };
        return pad(rval.toString(16)) + pad(gval.toString(16)) + pad(bval.toString(16));
    }
    var color, background, size;
    f = function (m) {
        var x, x1 = 500,
            x2 = 500,
            y, y1 = 500,
            y2 = 500,
            k, w, h, l;
        k = m.charAt(0);
        m.replace(r2, function ($0) {
            x = parseInt($0.slice(0, 3));
            y = parseInt($0.slice(4, 7));
            x1 = Math.min(x1, x);
            x2 = Math.max(x2, x);
            y1 = Math.min(y1, y);
            y2 = Math.max(y2, y);
        });
        if (k == 'S') {
            x2 = 1000 - x1;
            y2 = 1000 - y1;
        }
        w = (x2 - x1) * size;
        h = (y2 - y1) * size;
        l = o[k] || 0;
        l = (l * 75 + x1 - 400) * size;
        return '<div style="padding:10px;position:relative;background-repeat:no-repeat;background-origin:content-box;width:' + w + 'px;height:' + h + 'px;left:' + l + 'px;background-image:url(\'' + u + 'glyphogram.php?font=svg&text=' + m + '&line=' + color + '&fill=' + background + '&size=' + size + '\');"><span style="display:table-cell;vertical-align:middle;font-size:0%;height:inherit;">' + m + '</span></div>';
    };

    function fswReplace(node) {
        if (node.nodeType == 3) {
            color = rgbToHex(jQuery(node.parentNode).css('color'));
            background = jQuery(node.parentNode).css('background-color');
            parent = node.parentNode;
            while (background.toString() == 'rgba(0, 0, 0, 0)' || background.toString() == 'transparent') {
                parent = parent.parentNode;
                background = jQuery(parent).css('background-color');
            }
            background = rgbToHex(background);
            size = parseInt(jQuery(node.parentNode).css('font-size')) / 20;
            s1 = node.nodeValue;
            s2 = s1.replace(r, f);
            if (s1 != s2) {
                p = node.parentNode;
                d = document.createElement('div');
                d.innerHTML = s2;
                p.replaceChild(d, node);
            }
        } else {
            var nodes;
            if (node.nodeName != 'TEXTAREA') nodes = node.childNodes;
            if (nodes) {
                var i = nodes.length;
                while (i--) fswReplace(nodes[i]);
            }
        }
    };
    if (!node || !node.nodeType) node = document.body;
    fswReplace(node);
})
EOT;

$styled_bookmark = "javascript:" . $styled_base . "();";
$styled_bookmark = str_replace(" ","%20",$styled_bookmark);

$styled_script = "signwriting_styled=" . $styled_base . ";";
$styled_script = str_replace("%20"," ",$styled_script);
$styled_start = 'window.addEventListener ? window.addEventListener("load",signwriting_styled,false) : window.attachEvent && window.attachEvent("onload",signwriting_styled);';

$jquery_start = '';
?>
