<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage css
 */

/**
 * CSSセレクタレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCSSSelector extends BSArray {

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param mixed $value 要素
	 * @param boolean $position 先頭ならTrue
	 */
	public function setParameter ($name, $value, $position = self::POSITION_BOTTOM) {
		if ($value instanceof BSColor) {
			$value = $value->getContents();
		} else if (is_numeric($value)) {
			$value .= 'px';
		}
		if (($name = trim($name)) && ($value = trim($value))) {
			parent::setParameter($name, $value, $position);
		}
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return $this->join('; ', ':');
	}

	/**
	 * 文字列をパースし、属性を設定
	 *
	 * @access public
	 * @param string $contents 内容
	 */
	public function setContents ($contents) {
		foreach (BSString::explode(';', $contents) as $param) {
			$param = BSString::explode(':', $param);
			$this[$param[0]] = $param[1];
		}
	}
}

