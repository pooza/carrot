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
	private $renderer;

	/**
	 * @access public
	 * @param BSHost $path ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host = null, $port = null) {
		parent::__construct($host, $port);

		$this->renderer = new BSSmarty;
		$this->getRenderer()->setType('text/plain');
		if ($dir = BSController::getInstance()->getModule()->getDirectory('templates')) {
			$this->getRenderer()->setTemplatesDirectory($dir);
		}

		$request = BSRequest::getInstance();
		$this->getRenderer()->setAttribute('date', BSDate::getNow()->format());
		$this->getRenderer()->setAttribute('useragent', $request->getUserAgent()->getName());
		$this->getRenderer()->setAttribute('remote_host', $request->getHost()->getName());
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (!method_exists($this->getRenderer(), $method)) {
			throw new BSMagicMethodException('仮想メソッド"%s"は未定義です。', $method);
		}

		// 処理をレンダラーに委譲
		$args = array();
		for ($i = 0 ; $i < count($values) ; $i ++) {
			$args[] = '$values[' . $i . ']';
		}
		eval(sprintf('return $this->getRenderer()->%s(%s);', $method, implode(', ', $args)));
	}

	/**
	 * レンダラーを返す
	 *
	 * @access public
	 * @return BSSmarty レンダラー
	 */
	public function getRenderer () {
		return $this->renderer;
	}

	/**
	 * レンダラーを返す
	 *
	 * getRendererのエイリアス
	 *
	 * @access public
	 * @return BSSmarty レンダラー
	 * @final
	 */
	final public function getEngine () {
		return $this->getRenderer();
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
			} catch (BSViewException $e) {
			}
		}
	}

	/**
	 * 本文をレンダリング
	 *
	 * @access public
	 */
	public function render () {
		$lines = BSString::explode("\n", $this->getRenderer()->getContents());
		if (!$lines->count()) {
			throw new BSViewException('テンプレートが指定されていません。');
		}

		foreach ($lines as $key => $line) {
			if (BSString::isBlank($line)) { //空行を発見したらヘッダのパースをやめる
				$lines->removeParameter($key);
				break;
			} else if (preg_match('/^([a-z\-]+): *(.+)$/i', $line, $matches)) {
				$this->getMail()->setHeader($matches[1], $matches[2]);
				$lines->removeParameter($key);
			} else {
				break;
			}
		}

		$this->setBody($lines->join("\n"));
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param boolean $flag フラグ
	 *   self::TEST テスト送信
	 * @return string 送信完了時は最終のレスポンス
	 */
	public function send ($flag = null) {
		if (BSString::isBlank($this->getBody())) {
			$this->render();
		}
		return parent::send($flag);
	}
}

/* vim:set tabstop=4: */
