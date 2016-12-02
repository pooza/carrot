<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSKanaValidatorTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $validator = new BSKanaValidator);
		$this->assert('execute', $validator->execute('アイウエオ'));
		$this->assert('execute', $validator->execute('あいうえお'));
		$this->assert('execute', !$validator->execute('english'));
		$this->assert('execute', $validator->execute("\n"));
	}
}

/* vim:set tabstop=4: */
