<?php
/**
 * @package org.carrot-framework
 * @subpackage session.storage
 */

/**
 * memcacheセッションストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMemcacheSessionStorage implements BSSessionStorage {

	/**
	 * 初期化
	 *
	 * @access public
	 */
	public function initialize () {
		ini_set('session.save_handler', 'memcache');
		ini_set('session.save_path', BS_MEMCACHE_HOST . ':' . BS_MEMCACHE_PORT);
	}
}

/* vim:set tabstop=4: */
