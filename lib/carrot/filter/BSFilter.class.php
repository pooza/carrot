<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * 抽象フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSFilter extends BSParameterHolder {

	/**
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
				return BSController::getInstance();
			case 'request':
				return BSRequest::getInstance();
			case 'user':
				return BSUser::getInstance();
			case 'action':
				return BSController::getInstance()->getAction();
		}
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param mixed[] $parameters パラメータ
	 * @return boolean 初期化が成功すればTrue
	 */
	public function initialize ($parameters = array()) {
		$this->setParameters($parameters);
		return true;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param BSFilterChain $filters フィルタチェーン
	 */
	abstract public function execute (BSFilterChain $filters);
}

/* vim:set tabstop=4 ai: */
?>