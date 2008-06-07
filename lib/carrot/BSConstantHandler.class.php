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
	static private $instance;

	/**
	 * コンストラクタ
	 *
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
			self::$instance = new BSConstantHandler;
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @return mixed パラメータ
	 */
	public function getParameter ($name) {
		foreach (array('', 'APP_', 'BS_') as $prefix) {
			$fullname = strtoupper(BSString::underscorize($prefix . $name));
			if (defined($fullname)) {
				return constant($fullname);
			}
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
		if (defined($name)) {
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
		foreach (array('', 'APP_', 'BS_') as $prefix) {
			$fullname = strtoupper(BSString::underscorize($prefix . $name));
			if (defined($fullname)) {
				return true;
			}
		}
		return false;
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