<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * 定数ハンドラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSConstantHandler extends BSParameterHolder implements BSDictionary {
	const PREFIX = 'BS';
	private $prefix;

	/**
	 * @access public
	 */
	public function __construct ($prefix = '') {
		$this->prefix = BSString::toUpper(rtrim($prefix, '_'));
	}

	/**
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @return mixed パラメータ
	 */
	public function getParameter ($name) {
		foreach ($this->createKeys($name) as $name) {
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
		foreach ($this->createKeys($name) as $name) {
			if (defined($name)) {
				return true;
			}
		}
		return false;
	}
	private function createKeys ($name) {
		$name = (string)$name;
		$keys = new BSArray;
		if (BSString::isContain('::', $name)) {
			$keys[$name] = $name;
		} else {
			$key = BSString::toUpper($name);
			$keys[$key] = $key;
			foreach ([self::PREFIX, null] as $prefix) {
				$key = new BSArray;
				if (!BSString::isBlank($prefix)) {
					$key[] = self::PREFIX;
				}
				if (!BSString::isBlank($this->prefix)) {
					$key[] = $this->prefix;
				}
				$key[] = $name;
				$key = BSString::toUpper($key->join('_'));
				$keys[$key] = $key;
			}
		}
		return $keys;
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
		foreach ([null, '_' . $language] as $suffix) {
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
