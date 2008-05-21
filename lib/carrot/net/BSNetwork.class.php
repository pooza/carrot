<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net
 */

/**
 * ネットワークアドレス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSNetwork extends BSHost {

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $address CIDR形式のIPアドレス
	 */
	public function __construct ($address) {
		$this->address = new Net_IPv4;
		$this->setCIDR($address);
	}

	/**
	 * CIDR形式IPアドレスを返す
	 *
	 * @access public
	 * @return string CIDR形式ネットワークアドレス
	 */
	public function getCIDR () {
		return sprintf('%s/%s', $this->getAddress(), $this->getAttribute('bitmask'));
	}

	/**
	 * CIDR形式IPアドレスを設定
	 *
	 * @access public
	 * @param string $address CIDR形式ネットワークアドレス
	 */
	public function setCIDR ($address) {
		if (!preg_match("/^([0-9\.]+)\/([0-9]+)$/", $address, $matches)) {
			throw new BSNetException('"%s"のパースに失敗しました。', $address);
		}

		$this->setAddress($matches[1]);
		$net = $this->address->parseAddress($address);
		if ($net instanceof PEAR_Error) {
			throw new BSNetException('%sのパースに失敗しました。(%s)', $this, $net->message);
		}

		foreach (array('bitmask', 'netmask', 'network', 'broadcast', 'long') as $var) {
			$this->setAttribute($var, $net->$var);
		}
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('ネットワーク "%s"', $this->getCIDR());
	}
}

/* vim:set tabstop=4 ai: */
?>