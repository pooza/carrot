<?php
/**
 * @package org.carrot-framework
 * @subpackage string
 */

/**
 * フォーマット化文字列
 *
 * sprintfのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSStringFormat extends BSArray {

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param mixed $value 要素
	 * @param boolean $position 先頭ならTrue
	 */
	public function setParameter ($name, $value, $position = self::POSITION_BOTTOM) {
		if (version_compare(PHP_VERSION, '5.2.0', '<') && is_object($value)) {
			$value = $value->__toString();
		}
		parent::setParameter($name, $value);
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		try {
			return call_user_func_array('sprintf', $this->getParameters());
		} catch (Exception $e) {
			return $this->join(', ');
		}
	}

	/**
	 * @access public
	 * @return string
	 */
	public function __toString () {
		return $this->getContents();
	}
}

/* vim:set tabstop=4: */
