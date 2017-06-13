<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMIMEUtilityTest extends BSTest {
	public function execute () {
		$this->assert('decode', BSMIMEUtility::decode('=?utf-8?B?5pel5pys6Kqe44Gu44Oh44O844Or?=') == '日本語のメール');
		$this->assert('encode', BSMIMEUtility::encode('日本語のメール1') == '=?utf-8?B?5pel5pys6Kqe44Gu44Oh44O844Or?=1');
	}
}

/* vim:set tabstop=4: */
