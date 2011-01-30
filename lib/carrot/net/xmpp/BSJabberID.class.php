<?php
/**
 * @package org.carrot-framework
 * @subpackage net.xmpp
 */

/**
 * JabberID
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJabberID implements BSAssignable {
	private $contents;
	private $account;
	private $host;
	private $resource;

	/**
	 * @access public
	 * @param string $contents JabberID
	 */
	public function __construct ($contents) {
		$this->contents = $contents;
		if (!mb_ereg(BSJabberIDValidator::PATTERN, $this->contents, $matches)) {
			throw new BSXMPPException($this . 'が正しくありません。');
		}
		$this->account = $matches[1];
		$this->host = new BSHost($matches[2]);
		if (isset($matches[5])) {
			$this->resource = $matches[5];
		}
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
		return sprintf('JabberID "%s"', $this->getContents());
	}
}

/* vim:set tabstop=4: */
