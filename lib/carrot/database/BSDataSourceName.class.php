<?php
/**
 * @package org.carrot-framework
 * @subpackage database
 */

/**
 * データソース名
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSDataSourceName extends BSParameterHolder {
	private $name;
	private $contents;

	/**
	 * @access public
	 * @param mixed[] $params 要素の配列
	 */
	public function __construct ($contents, $name = 'default') {
		$this->contents = $contents;
		$this->name = $name;
		$this->parse();
	}

	/**
	 * DSN名を返す
	 *
	 * @access public
	 * @return string DSN名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return $this->contents;
	}

	/**
	 * データベースに接続して返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 * @abstract
	 */
	abstract public function getDatabase ();


	/**
	 * パスワードの候補を配列で返す
	 *
	 * @access protected
	 * @return BSArray パスワードの候補
	 */
	protected function getPasswords () {
		$constants = BSConstantHandler::getInstance();
		$passwords = new BSArray;
		if (!BSString::isBlank($password = $this->getConstant('password'))) {
			$passwords[] = BSCrypt::getInstance()->decrypt($password);
		}
		$passwords[] = $password;
		return $passwords;
	}

	/**
	 * DSNをパースしてパラメータに格納
	 *
	 * @access protected
	 */
	protected function parse () {
		$this['connection_name'] = $this->getName();
		$this['dsn'] = $this->getContents();
		$this['uid'] = $this->getConstant('uid');
		$this['password'] = $this->getConstant('password');
		$this['loggable'] = !!$this->getConstant('loggable');
	}

	/**
	 * 定数を返す
	 *
	 * @access public
	 * @param string $name 定数名
	 * @return string 定数
	 */
	public function getConstant ($name) {
		return BSConstantHandler::getInstance()->getParameter('PDO_' . $this->name . '_' . $name);
	}
}

/* vim:set tabstop=4: */
