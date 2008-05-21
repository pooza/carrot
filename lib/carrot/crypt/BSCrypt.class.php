<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage crypt
 */

/**
 * 暗号化
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSCrypt {
	private $engine;
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化は禁止
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSCrypt インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSCrypt();
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
	 * 未定義メソッドの呼び出し
	 *
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (!method_exists($this->engine, $method)) {
			throw new BSException('仮想メソッド"%s"は未定義です。', $method);
		}

		// 処理をエンジンに委譲
		$args = array();
		for ($i = 0 ; $i < count($values) ; $i ++) {
			$args[] = '$values[' . $i . ']';
		}
		eval(sprintf('return $this->engine->%s(%s);', $method, implode(', ', $args)));
	}

	/**
	 * 暗号化の方法を返す
	 *
	 * @access public
	 * @return string 暗号化の方法
	 */
	public function getMethod () {
		if (defined('BS_CRYPT_ENGINE')) {
			return BS_CRYPT_ENGINE;
		} else {
			return 'blowfish';
		}
	}

	/**
	 * 暗号化の方法を返す
	 *
	 * getMethodのエイリアス
	 *
	 * @access public
	 * @return string 暗号化の方法
	 * @final
	 */
	final public function getName () {
		return $this->getMethod();
	}

	/**
	 * 暗号化エンジンを返す
	 *
	 * @access public
	 * @return BSCryptEngine 暗号化エンジン
	 */
	public function getEngine () {
		if (!$this->engine) {
			$class = 'BS' . BSString::pascalize($this->getMethod()) . 'CryptEngine';
			$this->engine = new $class;
		}
		return $this->engine;
	}

	/**
	 * 暗号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 暗号化された文字列
	 */
	public function encrypt ($value) {
		if ($value) {
			return $this->getEngine()->encrypt($value);
		}
	}

	/**
	 * 複号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string 複号化された文字列
	 */
	public function decrypt ($value) {
		if ($value) {
			return $this->getEngine()->decrypt($value);
		}
	}

	/**
	 * 規定のソルトを返す
	 *
	 * @access public
	 * @return string ソルト
	 * @static
	 */
	static public function getDefaultSalt () {
		if (!defined('BS_CRYPT_SALT')) {
			throw new BSCryptException('BS_CRYPT_SALTが未定義です。');
		}
		return BS_CRYPT_SALT;
	}

	/**
	 * md5ダイジェストを返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string ダイジェスト文字列
	 * @static
	 */
	static public function getMD5 ($value) {
		if (extension_loaded('hash')) {
			return hash('md5', $value);
		} else {
			return md5($value);
		}
	}

	/**
	 * sha1ダイジェストを返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @return string ダイジェスト文字列
	 * @static
	 */
	static public function getSHA1 ($value) {
		if (extension_loaded('hash')) {
			return hash('sha1', $value);
		} else {
			return sha1($value);
		}
	}
}

/* vim:set tabstop=4 ai: */
?>