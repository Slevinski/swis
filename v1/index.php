<?php
//v1 compatible copy of main api.php
if (file_exists('api.php')) {
  chdir('..');
  include 'v1/api.php';
} else {
  chdir('..');
  include 'api.php';
}
?>
