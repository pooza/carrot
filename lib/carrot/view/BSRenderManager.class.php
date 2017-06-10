<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view
 */

/**
 * レンダーマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSRenderManager {
	use BSSingleton;
	private $memcache;

	/**
	 * @access protected
	 */
	protected function __construct () {
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
			$data = (new BSPHPSerializer)->decode($data);
			if (BSString::isBlank($data['contents'])) {
				return null;
			}

			$view = new BSView($action, 'Success');
			$view->setRenderer(new BSRawRenderer);
			$view->getRenderer()->setContents($data['contents']);

			foreach ($data['headers'] as $key => $value) {
				$view->setHeader($key, $value);
			}
			if ($header = $view->getHeader('content-type')) {
				$view->getRenderer()->setType($header->getContents());
			}
			return $view;
		}
	}

	/**
	 * レスポンスをキャッシュする
	 *
	 * @access public
	 * @param BSHTTPResponse $view キャッシュ対象
	 */
	public function cache (BSHTTPResponse $view) {
		if (BSString::isBlank($contents = $view->getRenderer()->getContents())) {
			return;
		}
		$data = ['headers' => [], 'contents' => $contents];
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

