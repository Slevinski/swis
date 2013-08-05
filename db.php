<?php
$db = new PDO(
  'mysql:host=signwriting.com;dbname=sworg3x_api',
  'sworg3x_api',
  '7sign&you!'
);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
?>