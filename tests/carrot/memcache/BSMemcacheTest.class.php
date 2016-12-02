<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMemcacheTest extends BSTest {
	public function execute () {
		if ($memcache = BSMemcacheManager::getInstance()->getServer()) {
			$memcache['hoge'] = 1;
			$this->assert('get_1', ($memcache['hoge'] == 1));
			$memcache->delete('hoge');
			$this->assert('get_2', ($memcache['hoge'] === false));
		}
	}
}

/* vim:set tabstop=4: */
