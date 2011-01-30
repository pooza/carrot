<?php
/**
 * @package org.carrot-framework
 * @subpackage net
 */

/**
 * ホストコンピュータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHost implements BSAssignable, BSImageContainer {
	protected $name;
	protected $address;
	protected $domain;

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
	 * 親ドメインを返す
	 *
	 * @access public
	 * @return string 親ドメイン
	 */
	public function getDomain () {
		if (!$this->domain) {
			$name = BSString::explode('.', $this->getName());
			$name->shift();
			$this->domain = $name->join('.');
		}
		return $this->domain;
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
	 * キャッシュをクリア
	 *
	 * @access public
	 * @param string $size
	 */
	public function clearImageCache ($size = 'favicon') {
		if ($file = $this->getImageFile($size)) {
			$file->clearImageCache();
		}
	}

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセルサイズ
	 * @param integer $flags フラグのビット列
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo ($size = 'favicon', $pixel = null, $flags = null) {
		if ($file = $this->getImageFile($size)) {
			$images = new BSImageManager;
			$info = $images->getImageInfo($file, $size, $pixel, $flags);
			$info['alt'] = $this->getID();
			return $info;
		}
	}

	/**
	 * 画像ファイルを返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return BSImageFile 画像ファイル
	 */
	public function getImageFile ($size = 'favicon') {
		$service = new BSGoogleFaviconsService;
		return $service->getImageFile($this);
	}

	/**
	 * 画像ファイルベース名を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return string 画像ファイルベース名
	 */
	public function getImageFileBaseName ($size) {
		return BSCrypt::getDigest($this->getID());
	}

	/**
	 * コンテナのIDを返す
	 *
	 * コンテナを一意に識別する値。
	 * ファイルならinode、DBレコードなら主キー。
	 *
	 * @access public
	 * @return integer ID
	 */
	public function getID () {
		$this->getName();
	}

	/**
	 * コンテナのラベルを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		return $this->getID();
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
