<?php
/**
 * @package org.carrot-framework
 * @subpackage net
 */

/**
 * ホストコンピュータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSHost implements BSAssignable {
	protected $name;
	protected $address;

	/**
	 * @access public
	 * @param string $address ホスト名又はIPアドレス
	 */
	public function __construct ($address) {
		// アドレスが列挙されていたり、ポート番号が付記されていたら、取り除く。
		$parts = mb_split('[:,]', $address);
		$address = $parts[0];

		if (mb_ereg('^[.[:digit:]]+$', $address)) {
			if (!long2ip(ip2long($address))) {
				throw new BSNetException($address . 'を名前解決できません。');
			}
			$this->address = $address;
			if (BS_NET_RESOLVABLE) {
				$this->name = gethostbyaddr($address);
			} else {
				$this->name = $address;
			}
		} else {
			$this->name = $address;
			$this->address = gethostbyname($this->name);
		}
	}

	/**
	 * IPアドレスを返す
	 *
	 * @access public
	 * @return string IPアドレス
	 */
	public function getAddress () {
		return $this->address;
	}

	/**
	 * ホスト名を返す
	 *
	 * @access public
	 * @return string FQDNホスト名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 名前解決が可能か？
	 *
	 * @access public
	 * @return boolean 可能ならばTrue
	 */
	public function isExists () {
		try {
			return !BSString::isBlank($this->getName());
		} catch (BSNetException $e) {
			return false;
		}
	}

	/**
	 * 異なるホストか？
	 *
	 * @access public
	 * @param BSHost $host 対象ホスト
	 * @return boolean 異なるホストならTrue
	 */
	public function isForeign (BSHost $host = null) {
		if (!$host) {
			$host = BSController::getInstance()->getHost();
		}
		return ($this->getName() != $host->getName());
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return get_object_vars($this);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return $this->getAddress();
	}
}

/* vim:set tabstop=4: */
