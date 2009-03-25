<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail
 */

/**
 * メールアドレス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMailAddress implements BSAssignable {
	private $contents;
	private $name;
	private $account;
	private $domain;
	private $mx = array();
	const PATTERN = '/^([0-9a-z_\.\+\-]+)@(([0-9a-z_\-]+\.)+[a-z]+)$/i';

	/**
	 * @access public
	 * @param string $contents メールアドレス
	 * @param string $name 名前
	 */
	public function __construct ($contents, $name = null) {
		if (BSString::isBlank($name) && preg_match('/^(.+) <(.+)>$/i', $contents, $matches)) {
			$name = $matches[1];
			$contents = $matches[2];
		}
		$this->contents = $contents;

		if (!preg_match(self::PATTERN, $this->contents, $matches)) {
			throw new BSMailException('%sはパターンにマッチしません。', $this);
		}
		$this->name = $name;
		$this->account = $matches[1];
		$this->domain = $matches[2];
	}

	/**
	 * 内容を返す
	 *
	 * getAddressのエイリアス
	 *
	 * @access public
	 * @return string メールアドレス
	 * @final
	 */
	final public function getContents () {
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
	 * 正しいドメインか？
	 *
	 * @access public
	 * @return boolean 正しいドメインならTrue
	 */
	public function isValidDomain () {
		$domains = $this->getMXRecords();
		$domains[] = $this->getDomainName();
		foreach ($domains as $domain) {
			$host = new BSHost($domain);
			if ($host->isExists()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * メールアドレスを書式化
	 *
	 * @access public
	 * @return string 書式化されたメールアドレス
	 */
	public function format () {
		if ($this->name) {
			return $this->name . ' <' . $this->contents . '>';
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
		foreach (BSMobileCarrier::getDomainSuffixes() as $type => $suffix) {
			if (strstr($this->getContents(), $suffix)) {
				return $type;
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
	public function getAssignValue () {
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
