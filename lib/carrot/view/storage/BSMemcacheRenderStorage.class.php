<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.storage
 */

/**
 * Memcacheレンダーストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMemcacheRenderStorage implements BSRenderStorage {
	private $memcache;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->memcache = BSMemcacheManager::getInstance()->getServer('render');
	}

	/**
	 * キャッシュを返す
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return BSView キャッシュ
	 */
	public function getCache (BSAction $action) {
		if ($data = $this->memcache[$action->digest()]) {
			$data = BSArray::create((new BSPHPSerializer)->decode($data));
			if (!BSString::isBlank($data['contents'])) {
				return $data;
			}
		}
	}

	/**
	 * レスポンスをキャッシュする
	 *
	 * @access public
	 * @param BSHTTPResponse $view キャッシュ対象
	 */
	public function cache (BSHTTPResponse $view) {
		$data = ['headers' => [], 'contents' => $view->getRenderer()->getContents()];
		foreach ($view->getHeaders() as $header) {
			if ($header->isVisible() && $header->isCacheable()) {
				$data['headers'][$header->getName()] = $header->getContents();
			}
		}
		$this->memcache[$view->getAction()->digest()] = (new BSPHPSerializer)->encode($data);
	}

	/**
	 * キャッシュを持っているか？
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return boolean キャッシュを持っていたらTrue
	 */
	public function hasCache (BSAction $action) {
		return !!$this->memcache[$action->digest()];
	}

	/**
	 * 全てのキャッシュをクリア
	 *
	 * @access public
	 */
	public function clear () {
		if (!$this->memcache->getAttribute('error')) {
			$this->memcache->clear();
		}
	}
}

