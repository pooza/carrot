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
	private $name;

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
			case 'manager':
				return BSUtility::executeMethod('BSTestManager', 'getInstance');
		}
	}

	/**
	 * テスト名を返す
	 *
	 * @access public
	 * @return string テスト名
	 */
	public function getName () {
		if (!$this->name) {
			if (mb_ereg('BS(.*)Test', get_class($this), $matches)) {
				$this->name = $matches[1];
			}
		}
		return $this->name;
	}

	/**
	 * テスト名にマッチするか？
	 *
	 * @access public
	 * @param string $name テスト名
	 * @param boolean マッチするならTrue
	 */
	public function isMatched ($name) {
		return BSString::isContain(
			BSString::toLower($name),
			BSString::toLower(get_class($this))
		);
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
			return $this->setError($name);
		}

		$message = new BSStringFormat('  %s OK');
		$message[] = $name;
		$this->manager->put($message);
	}

	/**
	 * エラーを登録
	 *
	 * @access public
	 * @param string $name アサーションの名前
	 * @param string $message エラーメッセージ
	 */
	public function setError ($name, $message = null) {
		$this->errors[] = $name;
		$message = new BSStringFormat('  %s NG!!!');
		$message[] = $name;
		$this->manager->put($message);
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
