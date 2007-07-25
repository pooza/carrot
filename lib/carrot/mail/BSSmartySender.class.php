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
	private $engine;

	/**
	 * 未定義メソッドの呼び出し
	 *
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (!method_exists($this->getEngine(), $method)) {
			throw new BSException('仮想メソッド"%s"は未定義です。', $method);
		}

		// 処理をエンジンに委譲
		$args = array();
		for ($i = 0 ; $i < count($values) ; $i ++) {
			$args[] = '$values[' . $i . ']';
		}
		eval(sprintf('return $this->getEngine()->%s(%s);', $method, implode(', ', $args)));
	}

	/**
	 * エンジンを返す
	 *
	 * @access public
	 * @return BSCSVData エンジン
	 */
	public function getEngine () {
		if (!$this->engine) {
			$this->engine = new BSSmarty();
		}
		return $this->engine;
	}

	/**
	 * 本文をレンダリング
	 *
	 * @access public
	 */
	public function render () {
		if (!$this->getEngine()->getTemplate()) {
			throw new BSSmartyException('テンプレートが指定されていません。');
		}
		$contents = BSString::convertEncoding(
			$this->getEngine()->getContents(),
			BSString::SCRIPT_ENCODING,
			BSString::TEMPLATE_ENCODING
		);
		$this->setBody($contents);
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param boolean $testmode テストモード
	 */
	public function send ($testmode = false) {
		if (!$this->getBody()) {
			$this->render();
		}
		parent::send($testmode);
	}
}

/* vim:set tabstop=4 ai: */
?>