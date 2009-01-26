<?php
/**
 * @package org.carrot-framework
 * @subpackage net.smtp
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
		if (!$this->renderer) {
			$this->renderer = new BSSmarty;
			$this->renderer->setEncoding('iso-2022-jp');
			if ($dir = BSController::getInstance()->getModule()->getDirectory('templates')) {
				$this->renderer->setTemplatesDirectory($dir);
			}
		}
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
		if (!$lines = explode("\n", $this->getRenderer()->getContents())) {
			throw new BSViewException('テンプレートが指定されていません。');
		}

		foreach ($lines as $line) {
			if ($line == '') { //空行を発見したらヘッダのパースをやめる
				array_shift($lines);
				break;
			} else if ($this->parseHeader($line)) {
				array_shift($lines);
			} else {
				break;
			}
		}
		$this->setBody(implode("\n", $lines));
	}

	/**
	 * 行をパースし、エンベロープフィールドなら適切に処理
	 *
	 * @access private
	 * @param string $line 行
	 * @return boolean 行がヘッダならばTrue
	 */
	private function parseHeader ($line) {
		if (!preg_match('/^([a-z\-]+): *(.+)$/i', $line, $matches)) {
			return false;
		}

		$value = $matches[2];
		switch ($key = BSString::capitalize($matches[1])) {
			case 'Subject':
				$this->setSubject($value);
				return true;
			case 'From':
				$this->setFrom(new BSMailAddress($value));
				return true;
			case 'To':
				$this->setTo(new BSMailAddress($value));
				return true;
			case 'Bcc':
				foreach (explode(',', $value) as $address) {
					$this->addBCC(new BSMailAddress($address));
				}
				return true;
			default:
				$this->setHeader($key, $value);
				return true;
		}
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param boolean $mode テストモード
	 * @return string 送信完了時は最終のレスポンス
	 */
	public function send ($mode = false) {
		if (!$this->getBody()) {
			$this->render();
		}
		return parent::send($mode);
	}
}

/* vim:set tabstop=4: */
