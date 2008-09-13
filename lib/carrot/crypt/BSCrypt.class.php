<?php
/**
 * @package org.carrot-framework
 * @subpackage crypt
 */

/**
 * 暗号化
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCrypt {
	private $engine;
	static private $instance;
	const WITH_BASE64 = 1;

	/**
	 * @access private
	 */
	private function __construct () {
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
			self::$instance = new BSCrypt;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (!method_exists($this->engine, $method)) {
			throw new BSMagicMethodException('仮想メソッド"%s"は未定義です。', $method);
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
		if ($method = BSController::getInstance()->getConstant('CRYPT_ENGINE')) {
			return $method;
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
	 * @param integer $option オプションのビット列、現状self::WITH_BASE64のみ。
	 * @return string 暗号化された文字列
	 */
	public function encrypt ($value, $option = self::WITH_BASE64) {
		$value = $this->getEngine()->encrypt($value);
		if ($option & self::WITH_BASE64) {
			$value = base64_encode($value);
		}
		return $value;
	}

	/**
	 * 複号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @param integer $option オプションのビット列、現状self::WITH_BASE64のみ。
	 * @return string 複号化された文字列
	 */
	public function decrypt ($value, $option = self::WITH_BASE64) {
		if ($option & self::WITH_BASE64) {
			$value = base64_decode($value);
		}
		$value = $this->getEngine()->decrypt($value);
		$value = trim($value);
		return $value;
	}

	/**
	 * 規定のソルトを返す
	 *
	 * @access public
	 * @return string ソルト
	 * @static
	 */
	static public function getDefaultSalt () {
		if (!$salt = BSController::getInstance()->getConstant('CRYPT_SALT')) {
			throw new BSCryptException('ソルトが未定義です。');
		}
		return $salt;
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