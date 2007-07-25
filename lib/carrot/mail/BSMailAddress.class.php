<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage mail
 */

/**
 * メールアドレス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSMailAddress.class.php 323 2007-05-15 11:51:34Z pooza $
 */
class BSMailAddress {
	private $contents;
	private $name;
	private $account;
	private $domain;
	private $mx = array();
	private static $bcc = array();
	const PATTERN = '/^([0-9a-z_\.\-]+)@(([0-9a-z_\-]+\.)+[a-z]+)$/i';
	const NO_ENCODE = false;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $contents メールアドレス
	 * @param string $name 名前
	 */
	public function __construct ($contents, $name = null) {
		$this->contents = $contents;

		if (!preg_match(self::PATTERN, $this->contents, $matches)) {
			throw new BSMailException('%sはパターンにマッチしません。', $this);
		}

		$this->name = $name;
		$this->account = $matches[1];
		$this->domain = $matches[2];
	}

	/**
	 * 内容を返す - getAddressのエイリアス
	 *
	 * @access public
	 * @return string メールアドレス
	 */
	public function getContents () {
		return $this->getAddress();
	}

	/**
	 * メールアドレスを返す
	 *
	 * @access public
	 * @return string メールアドレス
	 */
	public function getAddress () {
		return $this->contents;
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * ドメイン名を返す
	 *
	 * @access public
	 * @return string ドメイン名
	 */
	public function getDomainName () {
		return $this->domain;
	}

	/**
	 * ドメイン名に対応するMXレコードを返す
	 *
	 * @access public
	 * @return string[] MXレコードの配列
	 */
	public function getMXRecords () {
		if (!$this->mx) {
			getmxrr($this->getDomainName(), $this->mx);
		}
		return $this->mx;
	}

	/**
	 * 最優先のメールサーバを返す
	 *
	 * @access public
	 * @return BSHost メールサーバ
	 */
	public function getPrimaryMailServer () {
		if (!$hosts = $this->getMXRecords()) {
			$hosts = array($this->getDomainName());
		}

		try {
			return new BSHost($hosts[0]);
		} catch (BSNetException $e) {
			return null;
		}
	}

	/**
	 * メールアドレスを書式化
	 *
	 * @access public
	 * @return string 書式化されたメールアドレス
	 */
	public function format ($encodeFlag = true) {
		if ($this->name) {
			if ($encodeFlag) {
				return BSSMTP::base64Encode($this->name) . ' <' . $this->contents . '>';
			} else {
				return $this->name . ' <' . $this->contents . '>';
			}
		} else {
			return $this->contents;
		}
	}

	/**
	 * キャリアを返す
	 *
	 * @access public
	 * @return string キャリアを示す文字
	 */
	public function getCarrier () {
		foreach (BSMobileUserAgent::getDomainSuffixes() as $type => $suffix) {
			if (strstr($this->getContents(), $suffix)) {
				return $type;
			}
		}
	}

	/**
	 * BCC送信先を返す
	 *
	 * @access public
	 * @return BSMailAddress[] メールアドレスの配列
	 * @static
	 */
	public static function getBCCAddresses () {
		if (!defined('BS_SMTP_BCC_EMAIL')) {
			return array();
		}

		if (!self::$bcc) {
			foreach (explode(',', BS_SMTP_BCC_EMAIL) as $address) {
				self::$bcc[] = new BSMailAddress($address);
			}
		}
		return self::$bcc;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('メールアドレス "%s"', $this->contents);
	}
}

/* vim:set tabstop=4 ai: */
?>