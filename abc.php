<?php
$v = array(array(5,6), array(1,2), array(1,3));

usort($v, fn($a, $b) => $a[1] < $b[1]);

print_r($v);
?>