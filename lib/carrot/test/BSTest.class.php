<?php
/**
 * @package org.carrot-framework
 * @subpackage test
 */

/**
 * 基底テスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSTest {
	private $errors;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->errors = new BSArray;
	}

	/**
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
			case 'request':
			case 'user':
				return BSUtility::executeMethod($name, 'getInstance');
		}
	}

	/**
	 * 実行
	 *
	 * @access public
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
				return $this->setError($name);
			}
		} catch (Exception $e) {
			return $this->setError($name, $e->getMessage());
		}
		print '  ' . $name . " OK\n";
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
		print '  ' . $name . ' NG ' . $message . " !!!!!!!!!!\n";
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
