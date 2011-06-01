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
			foreach (array('images', 'carrotlib', 'www', 'root') as $name) {
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

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @param string $redirectTo リダイレクト先
	 * @return string ビュー名
	 */
	public function redirect ($redirectTo) {
		if ($redirectTo instanceof BSHTTPRedirector) {
			$url = $redirectTo->createURL();
		} else {
			$url = BSURL::create();
			$url['path'] = $redirectTo;
		}

		$useragent = $this->request->getUserAgent();
		$url->setParameters($useragent->getQuery());
		if ($useragent->hasBug('cache_control')) {
			$url->setParameter('at', BSNumeric::getRandom());
		}

		$this->setHeader('Location', $url->getContents());
		return BSView::NONE;
	}
}

/* vim:set tabstop=4: */
