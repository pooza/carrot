<?php
/**
 * @package org.carrot-framework
 * @subpackage css
 */

/**
 * CSSセレクタレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
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
		}
		parent::setParameter($name, $value, $position);
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return BSString::toString($this, ':', '; ');
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
			if (!BSString::isBlank($value = trim($param[1]))) {
				$key = trim($param[0]);
				$this[$key] = $value;
			}
		}
	}
}

/* vim:set tabstop=4: */
