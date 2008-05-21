<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage mail
 */

/**
 * Smartyテンプレートによるメール送信
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSmartySender extends BSSMTP {
	private $renderer;

	/**
	 * 未定義メソッドの呼び出し
	 *
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (!method_exists($this->getRenderer(), $method)) {
			throw new BSException('仮想メソッド"%s"は未定義です。', $method);
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
			$module = BSController::getInstance()->getModule();
			if ($dir = $module->getDirectory()->getEntry('templates')) {
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
	 * 本文をレンダリング
	 *
	 * @access public
	 */
	public function render () {
		if (!$lines = explode("\n", $this->getRenderer()->getContents())) {
			throw new BSSmartyException('テンプレートが指定されていません。');
		}
		foreach ($lines as $line) {
			if ($line == '') { //空行を発見したらヘッダのパースをやめる
				array_shift($lines);
				break;
			} else if (preg_match('/^([a-z\-]+): (.+)$/i', $line, $matches)) {
				$name = BSString::pascalize($matches[1]);
				$value = self::base64Encode($matches[2]);
				if ($name == 'Subject') {
					$this->setSubject($value);
				} else {
					$this->setHeader($name, $value);
				}
				array_shift($lines);
			} else {
				break;
			}
		}
		$body = BSString::convertEncoding(
			implode("\n", $lines),
			BSString::SCRIPT_ENCODING,
			BSString::TEMPLATE_ENCODING
		);
		$this->setBody($body);
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

/* vim:set tabstop=4 ai: */
?>