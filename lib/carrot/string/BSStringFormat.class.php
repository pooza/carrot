<?php
/**
 * @package jp.co.b-shock.carrot
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
