<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.cache.storage
 */

/**
 * memcacheを用いたSmartyキャッシュストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMemcacheSmartyCacheStorage implements BSSmartyCacheStorage {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param BSSmarty $smarty
	 * @return string 利用可能ならTrue
	 */
	public function initialize (BSSmarty $smarty) {
		if (!BSMemcacheManager::getInstance()->isEnabled()) {
			return false;
		}
		if (!function_exists('serialize_cache_handler')) {
			function serialize_cache_handler ($action, $smarty, $contents, $template, $cache_id = null, $compile_id = null, $expire = null) {
				$server = BSMemcacheManager::getInstance()->getServer();
				$name = $template . '&' . $cache_id;
				switch ($action) {
					case 'read':
						return $server->get($name);
					case 'write':
						$server->set($name, $contents);
						return $contents;
					case 'clear':
						return true; //サポートしない
					default:
						$message = new BSStringFormat('不正なアクション "%s" です。');
						$message[] = $action;
						throw new BSSmartyException($message);
				}
			}
		}
		$smarty->cache_handler_func = 'serialize_cache_handler';
		$smarty->cache_lifetime = BS_SMARTY_CACHE_LIFE_TIME;
		$smarty->caching = 1;
		return true;
	}
}

/* vim:set tabstop=4: */
