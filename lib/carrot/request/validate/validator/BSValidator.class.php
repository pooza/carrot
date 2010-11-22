<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 抽象バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSValidator extends BSParameterHolder {
	protected $error;
	protected $controller;
	protected $request;
	protected $user;
	protected $manager;

	/**
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function __construct ($params = array()) {
		$this->controller = BSController::getInstance();
		$this->request = BSRequest::getInstance();
		$this->user = BSUser::getInstance();
		$this->manager = BSValidateManager::getInstance();
		$this->initialize($params);
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		return get_class($this);
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param mixed[] $params パラメータ
	 * @return boolean 初期化が成功すればTrue
	 */
	public function initialize ($params = array()) {
		$this->setParameters($params);
		return true;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 * @abstract
	 */
	abstract public function execute ($value);

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
	}
}

/* vim:set tabstop=4: */
