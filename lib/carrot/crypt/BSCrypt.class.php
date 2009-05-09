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
	const SHA1 = 1;
	const MD5 = 2;
	const PLAINTEXT = 4;

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
			self::$instance = new self;
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
		return BSUtility::executeMethod($this->engine, $method, $values);
	}

	/**
	 * 暗号化器を返す
	 *
	 * @access public
	 * @return BSCryptor 暗号化器
	 */
	public function getEngine () {
		if (!$this->engine) {
			$this->engine = BSClassLoader::getInstance()->getObject(BS_CRYPT_ENGINE, 'Cryptor');
		}
		return $this->engine;
	}

	/**
	 * 暗号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @param integer $flags フラグのビット列
	 *   self::WITH_BASE64 暗号化した後、更にBASE64でエンコード。
	 * @return string 暗号化された文字列
	 */
	public function encrypt ($value, $flags = self::WITH_BASE64) {
		$value = $this->getEngine()->encrypt($value);
		if ($flags & self::WITH_BASE64) {
			$value = BSMIMEUtility::encodeBase64($value);
		}
		return $value;
	}

	/**
	 * 複号化された文字列を返す
	 *
	 * @access public
	 * @param string $value 対象文字列
	 * @param integer $flags フラグのビット列
	 *   self::WITH_BASE64 暗号化する前に、BASE64デコード。
	 * @return string 複号化された文字列
	 */
	public function decrypt ($value, $flags = self::WITH_BASE64) {
		if ($flags & self::WITH_BASE64) {
			$value = BSMIMEUtility::decodeBase64($value);
		}
		$value = $this->getEngine()->decrypt($value);
		$value = trim($value);
		return $value;
	}

	/**
	 * パスワード認証
	 *
	 * @access public
	 * @param string $password 正規文字列
	 * @param string $challenge 認証対象
	 * @param integer $methods 許可すべき認証方法のビット列
	 * @return boolean 一致するならTrue
	 */
	public function auth ($password, $challenge, $methods = null) {
		if (!$methods) {
			$methods = self::SHA1 | self::MD5 | self::PLAINTEXT;
		}

		$targets = new BSArray;
		$targets[] = $this->encrypt($challenge);
		if ($methods & self::PLAINTEXT) {
			$targets[] = $challenge;
		}
		if ($methods & self::SHA1) {
			$targets[] = self::getSHA1($challenge);
		}
		if ($methods & self::MD5) {
			$targets[] = self::getMD5($challenge);
		}

		return $targets->isContain($password);
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

/* vim:set tabstop=4: */
