<?php
/**
 * @package org.carrot-framework
 * @subpackage test
 */

/**
 * 基底テスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSTest {
	private $errors;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->errors = new BSArray;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @return boolean 成功ならTrue
	 * @abstract
	 */
	abstract public function execute ();

	/**
	 * アサート
	 *
	 * @access public
	 * @param string $name アサーションの名前
	 * @param boolean $assertion アサーションの内容
	 */
	public function assert ($name, $assertion) {
		try {
			if (!$assertion) {
				$this->setError($name);
			}
		} catch (Exception $e) {
			$this->setError($name, $e->getMessage());
		}
	}

	/**
	 * エラーを登録
	 *
	 * @access public
	 * @param string $name アサーションの名前
	 * @param string $message エラーメッセージ
	 */
	public function setError ($name, $message = null) {
		$this->errors[] = new BSArray(array(
			'test' => get_class($this),
			'assert' => $name,
			'message' => $message,
		));
	}

	/**
	 * 全てのエラーを返す
	 *
	 * @access public
	 * @return BSArray 全てのエラー
	 */
	public function getErrors () {
		return $this->errors;
	}
}

/* vim:set tabstop=4: */
