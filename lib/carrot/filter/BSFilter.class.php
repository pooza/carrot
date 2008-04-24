<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * Filterのひな形
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSFilter extends ParameterHolder {

	/**
	 * プロパティ取得のオーバライド
	 *
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
		}
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param mixed[] $parameters パラメータ
	 * @return boolean 初期化が成功すればTrue
	 */
	public function initialize ($parameters = null) {
		if ($parameters) {
			$this->parameters += $parameters;
		}
		return true;
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param FilterChain $filters フィルタチェーン
	 */
	abstract public function execute (FilterChain $filters);
}

/* vim:set tabstop=4 ai: */
?>