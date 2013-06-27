<?php
include 'bsw.php';
$query ='select code,num from symbolgroup order by code';
$result = $iswa_db->query($query)->fetchAll();
$cnt = 0;
echo 'var keyISWA=[' . "\n";
$sep = '[';
foreach ($result as $row){
  $query = 'select code,var_num,vars,fills,rots from basesymbol ';
  $query .= 'where sg_code = ' . $row[0] . ' order by code';
  $result2 = $iswa_db->query($query)->fetchAll();
  echo '  ' . $sep . "\n";
  $sep = '],[';
  foreach ($result2 as $i=>$row2 ){
    echo '    ["' . dechex($row2[0]) . '",';
    echo '"' . base2view(dechex($row2[0])) . '",';
    echo $row2[1] . ',' . $row2[2] . ',' . $row2[3] . ',' . $row2[4] . "]";
    if ($i+1<count($result2)) echo ",";
    echo "\n";
  }
  $cnt++;
}
echo "  ]\n];";

?>
