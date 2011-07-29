<?php
/**
 * @package org.carrot-framework
 * @subpackage view
 */

/**
 * レンダーマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSRenderManager {
	private $caches;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->caches = new BSArray;
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSRenderManager インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * キャッシュを返す
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return BSView キャッシュ
	 */
	public function getCache (BSAction $action) {
		if (!$this->caches[$action->getRenderResource()]) {
			$this->caches[$action->getRenderResource()] = new BSArray;
		}
		if (!$this->caches[$action->getRenderResource()][$action->digest()]) {
			$dir = $this->getResourceDirectory($action);
			if ($file = $dir->getEntry($action->digest())) {
				$serializer = new BSPHPSerializer;
				$data = $serializer->decode($file->getContents());
				$view = new BSView($action, 'Success');
				$view->setRenderer(new BSRawRenderer);
				$view->getRenderer()->setContents($data['contents']);
				foreach ($data['headers'] as $key => $value) {
					$view->setHeader($key, $value);
				}
				if ($header = $view->getHeader('content-type')) {
					$view->getRenderer()->setType($header->getContents());
				}
				$this->caches[$action->getRenderResource()][$action->digest()] = $view;
			}
		}
		return $this->caches[$action->getRenderResource()][$action->digest()];
	}

	/**
	 * レスポンスをキャッシュする
	 *
	 * @access public
	 * @param BSHTTPResponse $view キャッシュ対象
	 */
	public function cache (BSHTTPResponse $view) {
		$cache = array(
			'headers' => array(),
			'contents' => null,
		);
		foreach ($view->getHeaders() as $header) {
			if ($header->isVisible() && $header->isCacheable()) {
				$cache['headers'][$header->getName()] = $header->getContents();
			}
		}
		$cache['contents'] = $view->getRenderer()->getContents();

		$file = BSFileUtility::createTemporaryFile();
		$serializer = new BSPHPSerializer;
		$file->setContents($serializer->encode($cache));
		$file->setMode(0666);
		$file->moveTo($this->getResourceDirectory($view->getAction()));
		$file->rename($view->getAction()->digest() . '.serialized');
	}

	/**
	 * キャッシュを持っているか？
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return boolean キャッシュを持っていたらTrue
	 */
	public function hasCache (BSAction $action) {
		if ($this->getResourceDirectory($action)->getEntry($action->digest())) {
			return !BSString::isBlank($this->getCache($action)->getRenderer()->getContents());
		}
		return false;
	}

	/**
	 * キャッシュをクリア
	 *
	 * @access public
	 * @param BSAction $action アクション
	 */
	public function removeCache (BSAction $action) {
		$this->getResourceDirectory($action)->clear();
	}

	/**
	 * 全てのキャッシュをクリア
	 *
	 * @access public
	 */
	public function clear () {
		BSFileUtility::getDirectory('output')->clear();
	}

	private function getResourceDirectory (BSAction $action) {
		$dir = BSFileUtility::getDirectory('output');
		if (!$entry = $dir->getEntry($action->getRenderResource())) {
			$entry = $dir->createDirectory($action->getRenderResource());
			$entry->setMode(0777);
		}
		$entry->setDefaultSuffix('.serialized');
		return $entry;
	}
}

/* vim:set tabstop=4: */
