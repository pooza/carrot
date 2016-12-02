<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mime.header
 */

/**
 * Hostヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHostMIMEHeader extends BSMIMEHeader {
	protected $name = 'Host';
	private $host;
	private $port;

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return BSHost 実体
	 */
	public function getEntity () {
		return $this->host;
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if ($contents instanceof BSHost) {
			$contents = $contents->getName();
		}
		parent::setContents($contents);
	}

	/**
	 * ヘッダの内容からパラメータを抜き出す
	 *
	 * @access protected
	 */
	protected function parse () {
		parent::parse();
		try {
			$parts = BSString::explode(':', $this->contents);
			$this->host = new BSHost($parts[0]);
			$this->port = $parts[1];
		} catch (BSNetException $e) {
		}
	}
}

/* vim:set tabstop=4: */
