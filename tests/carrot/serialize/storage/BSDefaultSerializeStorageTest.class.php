<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDefaultSerializeStorageTest extends BSTest {
	public function execute () {
		$storage = new BSDefaultSerializeStorage;
		if ($storage->initialize()) {
			$storage->setAttribute('hoge', '木の水晶球');
			$this->assert('getAttribute_1', ($storage->getAttribute('hoge') == '木の水晶球'));
			$storage->removeAttribute('hoge');
			$this->assert('getAttribute_2', BSString::isBlank($storage->getAttribute('hoge')));
		}
	}
}

/* vim:set tabstop=4: */
