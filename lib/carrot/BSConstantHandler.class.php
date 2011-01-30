<?php
/**
 * @package org.carrot-framework
 */

/**
 * 定数ハンドラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSConstantHandler extends BSParameterHolder implements BSDictionary {
	static private $instance;
	const PREFIX = 'BS';

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSConstantHandler インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @return mixed パラメータ
	 */
	public function getParameter ($name) {
		foreach ($this->getSuggestedNames($name) as $name) {
			if (defined($name)) {
				return constant($name);
			}
		}
	}

	/**
	 * パラメータを設定
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @param mixed $value 値
	 */
	public function setParameter ($name, $value) {
		if (defined($name = BSString::toUpper((string)$name))) {
			$message = new BSStringFormat('定数 "%s" は定義済みです。');
			$message[] = $name;
			throw new BadFunctionCallException($message);
		}
		define($name, $value);
	}

	/**
	 * 全てのパラメータを返す
	 *
	 * @access public
	 * @return mixed[] 全てのパラメータ
	 */
	public function getParameters () {
		$constants = get_defined_constants(true);
		return new BSArray($constants['user']);
	}

	/**
	 * パラメータが存在するか？
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @return boolean 存在すればTrue
	 */
	public function hasParameter ($name) {
		if (is_array($name) || is_object($name)) {
			return false;
		}
		foreach ($this->getSuggestedNames($name) as $name) {
			if (defined($name)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * 定数名の候補を返す
	 *
	 * @access private
	 * @param string $name 定数名
	 * @return BSArray 定数名の候補
	 */
	private function getSuggestedNames ($name) {
		$names = new BSArray;
		$names[] = $name;
		if (!BSString::isContain('::', $name)) {
			$names[] = self::PREFIX . '_' . $name;
			$names = BSString::toUpper($names);
		}
		return $names;
	}

	/**
	 * パラメータを削除
	 *
	 * @access public
	 * @param string $name パラメータ名
	 */
	public function removeParameter ($name) {
		throw new BadFunctionCallException('定数は削除できません。');
	}

	/**
	 * 翻訳して返す
	 *
	 * @access public
	 * @param string $label ラベル
	 * @param string $language 言語
	 * @return string 翻訳された文字列
	 */
	public function translate ($label, $language) {
		foreach (array(null, '_' . $language) as $suffix) {
			if ($this->hasParameter($label . $suffix)) {
				if (BSString::isBlank($value = $this[$label . $suffix])) {
					return '';
				} else {
					return $value;
				}
			}
		}
	}

	/**
	 * 辞書の名前を返す
	 *
	 * @access public
	 * @return string 辞書の名前
	 */
	public function getDictionaryName () {
		return get_class($this);
	}
}

/* vim:set tabstop=4: */
