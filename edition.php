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
 
 $semver = "1.0.0-rc";
 $ed_date = "May 6th, 2013";
 
function curPage() {
  $pageURL = 'http';
  if (@$_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  return str_replace ($_SERVER["PHP_SELF"],'/',$pageURL);
}

$signwriting_thin = <<<EOT
(function(node){var%20u='$swis_url',v='1.0.0-rc',s1,s2,d,p,r,r2,o,f;r=/[BLMR]([0-9]{3}x[0-9]{3})(S[123][0-9a-f]{2}[0-5][0-9a-f][0-9]{3}x[0-9]{3})*|S38[7-9ab][0-5][0-9a-f][0-9]{3}x[0-9]{3}/g;r2=/[0-9]{3}x[0-9]{3}/g;o={};o.L=-1;o.R=1;f=function(m){var%20x,x1=500,x2=500,y,y1=500,y2=500,k,w,h,l;k=m.charAt(0);m.replace(r2,function($0){x=parseInt($0.slice(0,3));y=parseInt($0.slice(4,7));x1=Math.min(x1,x);x2=Math.max(x2,x);y1=Math.min(y1,y);y2=Math.max(y2,y);});if(k=='S'){x2=1000-x1;y2=1000-y1;}w=x2-x1;h=y2-y1;l=o[k]||0;l=l*75+x1-400;return%20'<div%20style="padding:10px;position:relative;background-repeat:no-repeat;background-origin:content-box;width:'+w+'px;height:'+h+'px;left:'+l+'px;background-image:url(\''+u+'glyphogram.php?font=svg&text='+m+'\');"><span%20style="display:table-cell;vertical-align:middle;font-size:0%;height:inherit;">'+m+'%20</span></div>';};function%20fswReplace(node){if(node.nodeType==3){s1=node.nodeValue;s2=s1.replace(r,f);if(s1!=s2){p=node.parentNode;d=document.createElement('div');d.innerHTML=s2;p.replaceChild(d,node);}}else{var%20nodes;if(node.nodeName!='TEXTAREA')nodes=node.childNodes;if(nodes){var%20i=nodes.length;while(i--)fswReplace(nodes[i]);}}};if(!node||!node.nodeType)node=document.body;fswReplace(node);})
EOT;

$bookmark = "javascript:" . $signwriting_thin . "();";

$script = "signwriting_thin=" . $signwriting_thin . ";";
$script = str_replace("%20"," ",$script);
$autostart = 'window.addEventListener ? window.addEventListener("load",signwriting_thin,false) : window.attachEvent && window.attachEvent("onload",signwriting_thin);';
?>
