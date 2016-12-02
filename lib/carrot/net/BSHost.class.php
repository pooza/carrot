<?php
/**
 * @package jp.co.b-shock.carrot
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

		if (BSString::isBlank($address)) {
			throw new BSNetException('ホスト名又はIPv4アドレスが空欄です。');
		} else if (mb_ereg('^[.[:digit:]]+$', $address)) {
			if (!long2ip(ip2long($address))) {
				throw new BSNetException($address . 'は正しいIPv4アドレスではありません。');
			}
			$this->address = $address;
			$this->name = $address;
		} else {
			$this->name = $address;
		}
	}

	/**
	 * IPアドレスを返す
	 *
	 * @access public
	 * @return string IPアドレス
	 */
	public function getAddress () {
		if (!$this->address) {
			$this->address = gethostbyname($this->name);
		}
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
		return BSCrypt::digest($this->getID());
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
	public function getAssignableValues () {
		return get_object_vars($this);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return $this->getName();
	}
}

/* vim:set tabstop=4: */
