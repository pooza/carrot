<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * 定数ハンドラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSConstantHandler extends BSParameterHolder implements BSDictionary {

	/**
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @return mixed パラメータ
	 */
	public function getParameter ($name) {
		$name = strtoupper($name);
		if ($this->hasParameter($name)) {
			return constant($name);
		}
	}

	/**
	 * パラメータを設定する
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @param mixed $value 値
	 */
	public function setParameter ($name, $value) {
		$name = strtoupper($name);
		if ($this->hasParameter($name)) {
			throw new BSException('定数 "%s" は定義済みです。', $name);
		} else {
			define($name, $value);
		}
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
		$name = strtoupper($name);
		return defined($name);
	}

	/**
	 * パラメータを削除する
	 *
	 * @access public
	 * @param string $name パラメータ名
	 */
	public function removeParameter ($name) {
		throw new BSException('定数は削除できません。');
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
	 * 翻訳して返す
	 *
	 * @access public
	 * @param string $label ラベル
	 * @param string $language 言語
	 * @return string 翻訳された文字列
	 */
	public function translate ($label, $language) {
		$labels = array($label, $label . '_' . $language);
		foreach ($labels as $label) {
			if ($value = $this->getParameter($label)) {
				return $value;
			}
		}
	}
}

/* vim:set tabstop=4 ai: */
?>