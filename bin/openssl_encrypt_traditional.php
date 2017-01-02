#!/usr/local/bin/php
<?php
define('BS_ROOT_DIR', dirname(dirname(__FILE__)));
define('DOMAIN', basename(BS_ROOT_DIR));
require_once BS_ROOT_DIR . '/lib/Spyc.php';
require_once BS_ROOT_DIR . '/lib/carrot/crypt/cryptor/BSCryptor.interface.php';
require_once BS_ROOT_DIR . '/lib/carrot/crypt/cryptor/BSOpenSSLTraditionalCryptor.class.php';

function parse ($file) {
  $path = BS_ROOT_DIR . '/webapp/config/constant/' . $file . '.yaml';
  return flatten('bs', Spyc::YAMLLoad($path));
}

function flatten ($prefix, $node) {
  $values = [];
  if (is_array($node)) {
    foreach ($node as $key => $value) {
      $values += flatten($prefix . '_' . $key, $value);
    }
  } else {
    $values[strtoupper($prefix)] = $node;
  }
  return $values;
}

foreach ([DOMAIN, 'application', 'carrot'] as $file) {
  foreach (parse($file) as $key => $value) {
    if (!defined($key)) {
      define($key, $value);
    }
  }
}

$cryptor = new BSOpenSSLTraditionalCryptor;
$encrypted = base64_encode($cryptor->encrypt($_SERVER['argv'][1]));
$decrypted = $cryptor->decrypt(base64_decode($encrypted));
?>
source:       <?= $_SERVER['argv'][1] ?> 
method:       AES-128-ECB
encrypted:    <?= $encrypted ?> 
-> decrypted: <?= $decrypted ?> 
