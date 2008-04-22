<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net
 */

BSController::includeLegacy('/pear/Net/IPv4.php');

/**
 * ホスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSHost {
	protected $address;
	protected $fqdn;
	const DEFAULT_SOCKET_CLASS = 'BSSocket';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $address ホスト名又はIPアドレス
	 */
	public function __construct ($address) {
		$this->address = new Net_IPv4();

		if (preg_match("/^[0-9\.]+$/", $address)) {
			$this->setAddress($address);
		} else {
			$this->setName($address);
		}
	}

	/**
	 * IPアドレスを返す
	 *
	 * @access public
	 * @return string IPアドレス
	 */
	public function getAddress () {
		return $this->getAttribute('ip');
	}

	/**
	 * IPアドレスを設定
	 *
	 * @access public
	 * @param string $address IPアドレス
	 */
	public function setAddress ($address) {
		$this->setAttribute('ip', $address);
		if (!$this->address->validateIP($address)) {
			throw new BSNetException('"%s"の名前解決に失敗しました。', $this);
		}
	}

	/**
	 * ホスト名を返す
	 *
	 * @access public
	 * @return string FQDNホスト名
	 */
	public function getName () {
		if (!$this->fqdn) {
			if (BSController::getInstance()->isResolvable()) {
				$this->fqdn = gethostbyaddr($this->getAddress());
			} else {
				$this->fqdn = $this->getAddress();
			}
		}
		return $this->fqdn;
	}

	/**
	 * ホスト名を設定
	 *
	 * @access public
	 * @param string $name FQDNホスト名
	 */
	public function setName ($name) {
		if (!$address = gethostbyname($name)) {
			throw new BSNetException('"%s"は正しくないFQDN名です。', $name);
		}
		$this->fqdn = $name;
		$this->setAddress($address);
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed 属性
	 */
	public function getAttribute ($name) {
		return $this->address->$name;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		$this->address->$name = $value;
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		return get_object_vars($this->address);
	}

	/**
	 * ソケットを返す
	 *
	 * @access public
	 * @param integer $port ポート
	 * @param string $class クラス名
	 * @return BSSocket ソケット
	 */
	public function getSocket ($port, $class = self::DEFAULT_SOCKET_CLASS) {
		return new $class($this, $port);
	}

	/**
	 * インスタンスはネットワーク内のノードか？
	 *
	 * @access public
	 * @param BSNetwork $network 評価対象ネットワーク
	 * @return boolean ネットワーク内ならTrue
	 */
	public function isInNetwork (BSNetwork $network) {
		return $this->address->ipInNetwork($this->getAddress(), $network->getCIDR());
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return $this->getAddress();
	}
}

/* vim:set tabstop=4 ai: */
?>