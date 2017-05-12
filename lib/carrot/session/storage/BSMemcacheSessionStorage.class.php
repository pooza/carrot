<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage session.storage
 */

/**
 * memcacheセッションストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMemcacheSessionStorage implements BSSessionStorage {

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		if (!BSMemcacheManager::getInstance()->isEnabled()) {
			return false;
		}
		$path = new BSStringFormat('%s:%s');
		$path[] = BS_MEMCACHE_DEFAULT_HOST;
		$path[] = BS_MEMCACHE_DEFAULT_PORT;
		ini_set('session.save_handler', 'memcached');
		ini_set('session.save_path', $path->getContents());
		return true;
	}
}

