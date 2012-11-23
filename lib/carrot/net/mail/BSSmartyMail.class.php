<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail
 */

/**
 * Smarty機能を内蔵したメールレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSmartyMail extends BSMail {

	/**
	 * 既定レンダラーを生成して返す
	 *
	 * @access protected
	 * @return BSRenderer 既定レンダラー
	 */
	protected function createRenderer () {
		$renderer = new BSSmarty;
		$renderer->setType(BSMIMEType::getType('txt'));
		$renderer->setEncoding('iso-2022-jp');
		$renderer->addOutputFilter('mail');
		if ($module = BSController::getInstance()->getModule()) {
			if ($dir = $module->getDirectory('templates')) {
				$renderer->registerDirectory($dir);
			}
		}
		$renderer->setAttribute('date', BSDate::getNow());
		$renderer->setAttribute('client_host', BSRequest::getInstance()->getHost());
		$renderer->setAttribute('server_host', BSController::getInstance()->getHost());
		return $renderer;
	}

	/**
	 * レンダラーを設定
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 * @param integer $flags フラグのビット列
	 *   BSMIMEUtility::WITHOUT_HEADER ヘッダを修正しない
	 *   BSMIMEUtility::WITH_HEADER ヘッダも修正
	 */
	public function setRenderer (BSRenderer $renderer, $flags = BSMIMEUtility::WITH_HEADER) {
		if (!($renderer instanceof BSSmarty)) {
			throw new BSMailException('レンダラー形式が正しくありません。');
		}
		parent::setRenderer($renderer, $flags);
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 */
	public function send () {
		$this->getRenderer()->render();
		foreach ($this->getRenderer()->getHeaders() as $key => $value) {
			$this->setHeader($key, $value);
		}
		if ($file = $this->getFile()) {
			$file->delete();
		}
		parent::send();
	}
}

/* vim:set tabstop=4: */
