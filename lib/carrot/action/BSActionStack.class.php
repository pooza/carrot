<?php
/**
 * @package org.carrot-framework
 * @subpackage action
 */

/**
 * アクションスタック
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSActionStack implements IteratorAggregate {
	private $stack;
	static private $instance;
	const LIMIT = 20;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->stack = new BSArray;
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSActionStack インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSActionStack;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * アクションをスタックに加える
	 *
	 * @access public
	 * @param BSAction $action アクション
	 */
	public function register (BSAction $action) {
		if (self::LIMIT < $this->getSize()) {
			throw new BSRegisterException('フォワードが多すぎます。');
		}
		$this->stack[] = $action;
	}

	/**
	 * 最初のアクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getFirstEntry () {
		return $this->getIterator()->getFirst();
	}

	/**
	 * 最後のアクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getLastEntry () {
		return $this->getIterator()->getLast();
	}

	/**
	 * 登録済みのアクション数を返す
	 *
	 * @access public
	 * @return integer 登録数
	 */
	public function getSize () {
		return count($this->stack);
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return $this->stack->getIterator();
	}
}

/* vim:set tabstop=4 ai: */
?>
