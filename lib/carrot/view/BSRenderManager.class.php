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
	use BSSingleton, BSBasicObject;
	private $storage;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->storage = $this->loader->createObject(BS_RENDER_STORAGE, 'RenderStorage');
	}

	/**
	 * キャッシュを返す
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return BSView キャッシュ
	 */
	public function getCache (BSAction $action) {
		if ($data = $this->storage->getCache($action)) {
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
		if (!BSString::isBlank($contents = $view->getRenderer()->getContents())) {
			$this->storage->cache($view);
		}
	}

	/**
	 * キャッシュを持っているか？
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return boolean キャッシュを持っていたらTrue
	 */
	public function hasCache (BSAction $action) {
		return $this->storage->hasCache($action);
	}

	/**
	 * 全てのキャッシュをクリア
	 *
	 * @access public
	 */
	public function clear () {
		$this->storage->clear();
	}
}

