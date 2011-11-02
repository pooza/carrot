<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMemcacheSerializeStorageTest extends BSTest {
	public function execute () {
		$storage = new BSMemcacheSerializeStorage;
		if ($storage->initialize()) {
			$key = get_class($this);
			$storage->setAttribute($key, '木の水晶球');
			$this->assert('getAttribute_1', ($storage->getAttribute($key) == '木の水晶球'));
			$storage->removeAttribute($key);
			$this->assert('getAttribute_2', BSString::isBlank($storage->getAttribute($key)));
		}
	}
}

/* vim:set tabstop=4: */
