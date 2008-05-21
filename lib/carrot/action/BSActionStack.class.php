<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage action
 */

/**
 * アクションスタック
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
class BSActionStack {
	private $stack;
	static private $instance;

	/**
	 * コンストラクタ
	 *
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
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * アクションをスタックに加える
	 *
	 * @access public
	 * @param BSAction $action アクション
	 */
	public function register (BSAction $action) {
		$this->stack[] = $action;
	}

	/**
	 * 最初のアクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getFirstEntry () {
		return $this->stack[0];
	}

	/**
	 * 最後のアクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getLastEntry () {
		return $this->stack[count($this->stack) - 1];
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
}

/* vim:set tabstop=4 ai: */
?>
