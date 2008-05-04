<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * フィルタチェーン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
class BSFilterChain {
	private $chain;
	private $index = -1;

	/**
	 * コンストラクタ
	 *
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
		if ($this->index < count($this->chain)) {
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