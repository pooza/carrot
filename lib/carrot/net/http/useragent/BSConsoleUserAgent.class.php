<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.useragent
 */

/**
 * CLI環境用 ダミーユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSConsoleUserAgent extends BSUserAgent {

	/**
	 * ビューを初期化
	 *
	 * @access public
	 * @param BSSmartyView 対象ビュー
	 * @return boolean 成功時にTrue
	 */
	public function initializeView (BSSmartyView $view) {
		$view->getRenderer()->setUserAgent($this);
		$view->setAttributes($this->request->getAttributes());
		$view->setAttribute('module', $view->getModule());
		$view->setAttribute('action', $view->getAction());
		$view->setAttribute('errors', $this->request->getErrors());
		$view->setAttribute('params', $this->request->getParameters());
		$view->setAttribute('credentials', $this->user->getCredentials());
		$view->setAttribute('is_debug', BS_DEBUG);
		return true;
	}

	/**
	 * セッションハンドラを生成して返す
	 *
	 * @access public
	 * @return BSSessionHandler
	 */
	public function createSession () {
		return new BSConsoleSessionHandler;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '^Console$';
	}
}

