<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.smtp
 */

/**
 * Smartyテンプレートによるメール送信
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSmartySender extends BSSMTP {

	/**
	 * @access public
	 * @param mixed $host ホスト
	 * @param integer $port ポート
	 * @param string $protocol プロトコル
	 *   BSNetworkService::TCP
	 *   BSNetworkService::UDP
	 */
	public function __construct ($host = null, $port = null, $protocol = BSNetworkService::TCP) {
		parent::__construct($host, $port, $protocol);

		$renderer = new BSSmarty;
		$renderer->setType(BSMIMEType::getType('txt'));
		$renderer->setEncoding('iso-2022-jp');
		$renderer->addOutputFilter('mail');

		if ($module = BSController::getInstance()->getModule()) {
			if ($dir = $module->getDirectory('templates')) {
				$renderer->setTemplatesDirectory($dir);
			}
		}

		$renderer->setAttribute('date', BSDate::getNow());
		$renderer->setAttribute('client_host', BSRequest::getInstance()->getHost());
		$renderer->setAttribute('useragent', BSRequest::getInstance()->getUserAgent());
		$this->getMail()->setRenderer($renderer);
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		return BSUtility::executeMethod($this->getRenderer(), $method, $values);
	}

	/**
	 * レンダラーを返す
	 *
	 * @access public
	 * @return BSSmarty レンダラー
	 */
	public function getRenderer () {
		return $this->getMail()->getRenderer();
	}

	/**
	 * テンプレートを設定
	 *
	 * @access public
	 * @param string $template テンプレートファイル名
	 */
	public function setTemplate ($template) {
		foreach (array($template, $template . '.mail') as $name) {
			try {
				$this->getRenderer()->setTemplate($template);
				$this->getMail()->clearContents();
			} catch (BSViewException $e) {
			}
		}
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param integer $flags フラグのビット列
	 *   self::TEST テスト送信
	 * @return string 送信完了時は最終のレスポンス
	 */
	public function send ($flags = null) {
		$this->render();
		foreach ($this->getRenderer()->getHeaders() as $key => $value) {
			$this->getMail()->setHeader($key, $value);
		}
		return parent::send($flags);
	}
}

/* vim:set tabstop=4: */
