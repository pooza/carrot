#!/usr/local/bin/php
<?php
$timeTarget = 0.05;

$cost = 8;
do {
  $cost ++;
  $start = microtime(true);
  password_hash('test', PASSWORD_DEFAULT, ['cost' => $cost]);
  $end = microtime(true);
} while (($end - $start) < $timeTarget);

$source = $_SERVER['argv'][1];
$hash = password_hash($source, PASSWORD_DEFAULT, ['cost' => $cost]);
$info = password_get_info($hash);
?>
source: <?= $source ?> 
hash:   <?= $hash ?> 
algo:   <?= $info['algoName'] ?> 
cost:   <?= $info['options']['cost'] ?> 
verify: <?= password_verify($source, $hash) ? 'OK' : 'NG'; ?> 
