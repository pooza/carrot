<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mail
 */

/**
 * メールアドレス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMailAddress implements BSAssignable {
	use BSBasicObject;
	private $contents;
	private $name;
	private $account;
	private $domain;
	private $url;
	private $mx = [];
	const PATTERN = '^([-+._[:alnum:]]+)@(([-._[:alnum:]])+[[:alpha:]]+)$';

	/**
	 * @access private
	 * @param string $contents メールアドレス
	 * @param string $name 名前
	 */
	private function __construct ($contents, $name = null) {
		if (BSString::isBlank($name) && mb_ereg('^(.+) *<(.+)>$', $contents, $matches)) {
			$name = $matches[1];
			$contents = $matches[2];
		}
		if (mb_ereg(self::PATTERN, $contents, $matches)) {
			$this->contents = $contents;
			$this->name = $name;
			$this->account = $matches[1];
			$this->domain = $matches[2];
		}
	}

	/**
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @return BSMailAddress インスタンス
	 * @static
	 */
	static public function create ($contents, $name = null) {
		$email = new self($contents, $name);
		if (!BSString::isBlank($email->getContents())) {
			return $email;
		}
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string メールアドレス
	 */
	public function getContents () {
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
	 * URLを返す
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		if (!$this->url) {
			$this->url = BSURL::create('mailto:' . $this->getContents());
		}
		return $this->url;
	}

	/**
	 * メールアドレスを書式化
	 *
	 * @access public
	 * @return string 書式化されたメールアドレス
	 */
	public function format () {
		if (BSString::isBlank($this->getName())) {
			return $this->getContents();
		} else {
			return $this->getName() . ' <' . $this->getContents() . '>';
		}
	}

	/**
	 * キャリアを返す
	 *
	 * @access public
	 * @return BSMobileCarrier キャリア
	 */
	public function getCarrier () {
		foreach (BSMobileCarrier::getNames() as $name) {
			$carrier = $this->loader->createObject($name, 'MobileCarrier');
			if (BSString::isContain($carrier->getDomainSuffix(), $this->getContents())) {
				return $carrier;
			}
		}
	}

	/**
	 * ケータイ用のアドレスか？
	 *
	 * @access public
	 * @return boolean ケータイ用ならTrue
	 */
	public function isMobile () {
		return !BSString::isBlank($this->getCarrier());
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function assign () {
		return $this->getContents();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('メールアドレス "%s"', $this->getContents());
	}
}

/* vim:set tabstop=4: */
