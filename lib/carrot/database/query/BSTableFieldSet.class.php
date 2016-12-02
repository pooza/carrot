<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.query
 */

/**
 * フィールド名の集合
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTableFieldSet extends BSArray {

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param mixed $value 要素
	 * @param boolean $position 先頭ならTrue
	 */
	public function setParameter ($name, $value, $position = self::POSITION_BOTTOM) {
		parent::setParameter($name, trim($value), $position);
	}

	/**
	 * パラメータをまとめて設定
	 *
	 * @access public
	 * @param mixed[] $params パラメータの配列
	 */
	public function setParameters ($params) {
		if (is_string($params)) {
			$params = BSString::explode(',', $params);
		}
		parent::setParameters($params);
	}

	/**
	 * 別の配列をマージ
	 *
	 * @access public
	 * @param mixed $values 配列
	 */
	public function merge ($values) {
		if (is_string($params)) {
			$params = BSString::explode(',', $params);
		}
		parent::merge($params);
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return $this->join(', ');
	}
}

/* vim:set tabstop=4: */
