<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMIMEUtilityTest extends BSTest {
	public function execute () {
		$this->assert('decode', BSMIMEUtility::decode('=?ISO-2022-JP?B?GyRCRnxLXDhsJE4lYSE8JWsbKEI=?=') == '日本語のメール');
		$this->assert('encode', BSMIMEUtility::encode('日本語のメール1') == '=?iso-2022-jp?B?GyRCRnxLXDhsJE4lYSE8JWsbKEI=?=1');
	}
}

/* vim:set tabstop=4: */
