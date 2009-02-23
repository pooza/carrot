<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.pop3
 */

/**
 * 受信メール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPOP3Mail extends BSMIMEDocument {
	private $id;
	private $size;
	private $server;
	private $executed;

	/**
	 * @access public
	 * @param BSPOP3 $server サーバ
	 * @param string $line レスポンス行
	 */
	public function __construct (BSPOP3 $server, $line) {
		$fields = BSString::explode(' ', $line);
		$this->id = $fields[0];
		$this->size = $fields[1];
		$this->server = $server;
		$this->executed = new BSArray;
	}

	/**
	 * IDを返す
	 *
	 * @access public
	 * @return integer ID
	 */
	public function getID () {
		return $this->id;
	}

	/**
	 * メッセージIDを返す
	 *
	 * @access public
	 * @return string メッセージID
	 */
	public function getMessageID () {
		if ($header = $this->getHeader('Message-ID')) {
			return $header->getEntity();
		}
	}

	/**
	 * ヘッダを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSMailHeader ヘッダ
	 */
	public function getHeader ($name) {
		if (!$this->getHeaders()->count()) {
			$this->queryHeaders();
		}
		return parent::getHeader($name);
	}

	/**
	 * 本文を取得
	 *
	 * @access public
	 */
	public function query () {
		$this->server->execute('RETR ' . $this->getID());
		$body = new BSArray($this->server->getLines());
		$body = $body->join("\n");
		$body = BSString::explode("\n\n", $body);

		if (!$this->getHeaders()->count()) {
			$this->parseHeaders($body[0]);
		}

		$body->removeParameter(0);
		$body = $body->join("\n\n");
		$body = preg_replace('/\.$/', '', $body);
		$body = trim($body);
		$body = BSString::convertEncoding($body);
		$this->parseBody($body);
		$this->executed['RETR'] = true;
	}

	/**
	 * ヘッダだけを取得
	 *
	 * @access public
	 */
	public function queryHeaders () {
		$this->server->execute('TOP ' . $this->getID() . ' 0');
		$headers = new BSArray($this->server->getLines());
		$this->parseHeaders($headers->join("\n"));
		$this->executed['TOP'] = true;
	}

	/**
	 * 本文を返す
	 *
	 * 添付メールの場合でも、素の本文を返す。
	 *
	 * @access public
	 * @return string 本文
	 */
	public function getBody () {
		if (!$this->executed['RETR']) {
			$this->query();
		}
		return parent::getBody();
	}

	/**
	 * サーバから削除
	 *
	 * @access public
	 */
	public function delete () {
		if (!$this->executed['DELE']) {
			$this->server->execute('DELE ' . $this->getID());
			BSController::getInstance()->putLog(
				sprintf('%sをサーバから削除しました。', $this),
				get_class($this)
			);
			$this->executed['DELE'] = true;
		}
	}
}

/* vim:set tabstop=4: */
