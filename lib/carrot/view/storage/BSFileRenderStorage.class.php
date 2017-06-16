<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.storage
 */

/**
 * ファイルレンダーストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFileRenderStorage implements BSRenderStorage {
	private $directory;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->directory = BSFileUtility::getDirectory('output');
	}

	/**
	 * キャッシュを返す
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return BSView キャッシュ
	 */
	public function getCache (BSAction $action) {
		if ($file = $this->directory->getEntry($action->digest())) {
			$data = BSArray::create((new BSPHPSerializer)->decode($file->getContents()));
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
		$file = $this->directory->createEntry($view->getAction()->digest());
		$file->setContents((new BSPHPSerializer)->encode($data));
	}

	/**
	 * キャッシュを持っているか？
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return boolean キャッシュを持っていたらTrue
	 */
	public function hasCache (BSAction $action) {
		return !!$this->directory->getEntry($action->digest());
	}

	/**
	 * 全てのキャッシュをクリア
	 *
	 * @access public
	 */
	public function clear () {
		$this->directory->clear();
	}
}

