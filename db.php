<?php
$db = new PDO(
  'mysql:host=db148c.pair.com;dbname=slevin_dev',
  'slevin_13_r',
  'BN9GPay2'
);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
?>