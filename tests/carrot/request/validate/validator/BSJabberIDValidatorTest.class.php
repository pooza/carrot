<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJabberIDValidatorTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $validator = new BSJabberIDValidator);
		$this->assert('execute', $validator->execute('tatsuya.koishi@gmail.com'));
		$this->assert('execute', $validator->execute('tatsuya.koishi@gmail.com/Home'));
		$this->assert('execute', !$validator->execute('tatsuya.koishi@gmail.com&&&&'));
	}
}

/* vim:set tabstop=4: */
