<?php
/**
 * @package org.carrot-framework
 * @subpackage controller
 */

/**
 * Webコントローラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSWebController extends BSController {

	/**
	 * 検索対象ディレクトリを返す
	 *
	 * @access public
	 * @return BSArray ディレクトリの配列
	 */
	public function getSearchDirectories () {
		if (!$this->searchDirectories) {
			$this->searchDirectories = new BSArray;
			foreach (['images', 'carrotlib', 'www', 'root'] as $name) {
				$this->searchDirectories[] = BSFileUtility::getDirectory($name);
			}
		}
		return $this->searchDirectories;
	}

	/**
	 * サーバサイドキャッシュが有効か
	 *
	 * @access public
	 * @return boolean 有効ならTrue
	 */
	public function hasServerSideCache () {
		return (BS_APP_HTTP_CACHE_MODE == 'public');
	}
}

/* vim:set tabstop=4: */
