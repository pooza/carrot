<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xmpp
 */

/**
 * JabberID
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSJabberID.class.php 5 2007-07-25 08:04:01Z pooza $
 */
class BSJabberID {
	private $contents;
	private $account;
	private $host;
	private $resource;
	const PATTERN = '/^([0-9a-z_\.\-]+)@(([0-9a-z_\-]+\.)+[a-z]+)\/([0-9a-z_\-]+)$/i';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $contents JabberID
	 */
	public function __construct ($contents) {
		$this->contents = $contents;
		if (!preg_match(self::PATTERN, $this->contents, $matches)) {
			throw new BSXMPPException('%sが正しくありません。', $this);
		}
		$this->account = $matches[1];
		$this->host = new BSHost($matches[2]);
		$this->resource = $matches[4];
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string JabberID
	 */
	public function getContents () {
		return $this->contents;
	}

	/**
	 * アカウントを返す
	 *
	 * @access public
	 * @return string アカウント
	 */
	public function getAccount () {
		return $this->account;
	}

	/**
	 * ホストを返す
	 *
	 * @access public
	 * @return BSHost ホスト
	 */
	public function getHost () {
		return $this->host;
	}

	/**
	 * リソース名を返す
	 *
	 * @access public
	 * @return string リソース名
	 */
	public function getResource () {
		return $this->resource;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('JabberID "%s"', $this->getContents());
	}
}

/* vim:set tabstop=4 ai: */
?>