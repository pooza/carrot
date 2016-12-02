<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.validate.validator
 */

/**
 * 抽象バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSValidator extends BSParameterHolder {
	protected $error;

	/**
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function __construct ($params = []) {
		$this->initialize($params);
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
				return BSValidateManager::getInstance();
		}
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
	public function initialize ($params = []) {
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
