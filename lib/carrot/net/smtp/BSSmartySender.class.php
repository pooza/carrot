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
 * @version $Id: BSSmartySender.class.php 86 2007-11-04 15:46:47Z pooza $
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
			$this->renderer = new BSSmarty();
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
		if (!$this->getRenderer()->getTemplate()) {
			throw new BSSmartyException('テンプレートが指定されていません。');
		}
		$contents = BSString::convertEncoding(
			$this->getRenderer()->getContents(),
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