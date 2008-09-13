<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * フィルタチェーン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSFilterChain {
	private $chain;
	private $index = -1;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->chain = new BSArray;
	}

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		$this->index ++;
		if ($this->index < $this->chain->count()) {
			$this->chain[$this->index]->execute($this);
		}
	}

	/**
	 * フィルタをチェーンに加える
	 *
	 * @access public
	 * @param BSFilter $filter フィルタ
	 */
	public function register (BSFilter $filter) {
		$this->chain[] = $filter;
	}
}

/* vim:set tabstop=4 ai: */
?>