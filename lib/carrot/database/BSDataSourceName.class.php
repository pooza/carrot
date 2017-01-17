<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * データソース名
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSDataSourceName extends BSParameterHolder {

	/**
	 * @access public
	 * @param mixed[] $params 要素の配列
	 */
	public function __construct ($contents, $name = 'default') {
		$this['connection_name'] = $name;
		$this['dbms'] = $this->getDBMS();
		$this['dsn'] = $contents;
		$this['uid'] = $this->getConstant('uid');
		$this['password'] = $this->getConstant('password');
		$this['loggable'] = !!$this->getConstant('loggable');
	}

	/**
	 * DSN名を返す
	 *
	 * @access public
	 * @return string DSN名
	 */
	public function getName () {
		return $this['connection_name'];
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return $this['dsn'];
	}

	/**
	 * DBMS名を返す
	 *
	 * @access public
	 * @return string DBMS名
	 * @abstract
	 */
	abstract public function getDBMS ();

	/**
	 * データベースに接続して返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 * @abstract
	 */
	abstract public function connect ();

	/**
	 * 復号したパスワードを返す
	 *
	 * @access public
	 * @return string パスワード
	 */
	public function decryptPassword () {
		return BSCrypt::getInstance()->decrypt($this['password']);
	}

	/**
	 * 定数を返す
	 *
	 * @access public
	 * @param string $name 定数名
	 * @return string 定数
	 */
	public function getConstant ($name) {
		$constants = new BSConstantHandler('PDO_' . $this->getName());
		return $constants[$name];
	}
}

/* vim:set tabstop=4: */
