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
 * @version $Id$
 */
class BSStringFormat extends BSArray {

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return call_user_func_array('sprintf', $this->getParameters());
	}
}

/* vim:set tabstop=4: */
