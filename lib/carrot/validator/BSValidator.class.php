<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage validator
 */

/**
 * 抽象バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSValidator extends ParameterHolder {

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
	 * @param mixed $value バリデート対象
	 * @param string $error エラーメッセージ代入先
	 * @return boolean 妥当な値ならばTrue
	 * @abstract
	 */
	abstract function execute (&$value, &$error);
}

/* vim:set tabstop=4 ai: */
?>