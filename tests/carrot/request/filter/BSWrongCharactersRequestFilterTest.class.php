<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSWrongCharactersRequestFilterTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $filter = new BSWrongCharactersRequestFilter);

		$this->request[get_class($this)] = '㈱';
		$filter->execute();
		$this->assert('convert', $this->request[get_class($this)] == '(株)');
	}
}

/* vim:set tabstop=4: */
