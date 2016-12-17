<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSOpenSSLCryptorTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $cryptor = new BSOpenSSLCryptor);
		$data = BSUtility::getUniqueID();
		$this->assert('encrypt', $encrypted = $cryptor->encrypt($data));
		$this->assert('decrypt', $decrypted = $cryptor->decrypt($encrypted));
		$this->assert('equal', $data === $decrypted);
	}
}

/* vim:set tabstop=4: */
