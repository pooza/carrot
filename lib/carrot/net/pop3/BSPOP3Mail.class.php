<?php
/**
 * @package org.carrot-framework
 * @subpackage net.pop3
 */

/**
 * 受信メール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPOP3Mail {
	private $id;
	private $size;
	private $socket;
	private $headers;
	private $fields;

	/**
	 * @access public
	 * @param string $line レスポンス行
	 * @param BSPOP3 $socket ソケット
	 */
	public function __construct (BSPOP3 $socket, $line) {
		$fields = BSString::explode(' ', $line);
		$this->id = $fields[0];
		$this->size = $fields[1];
		$this->socket = $socket;
		$this->fields = new BSArray;
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
		return $this->getField('message-id');
	}

	/**
	 * フィールドを返す
	 *
	 * 生のヘッダではなく、適宜パースする
	 *
	 * @access public
	 * @param string $name ヘッダ名
	 * @return mixed ヘッダ
	 */
	public function getField ($name) {
		if (!$this->fields->hasParameter($name)) {
			if (BSString::isBlank($value = BSSMTP::base64Decode($this->getHeader($name)))) {
				return null;
			}
			switch ($name = strtolower($name)) {
				case 'from':
				case 'reply-to':
					$this->fields[$name] = new BSMailAddress($value);
					break;
				case 'to':
				case 'cc':
					$this->fields[$name] = new BSArray;
					foreach (preg_split('/[,;]/', $value) as $address) {
						$this->fields[$name][] = new BSMailAddress($address);
					}
					break;
				case 'date':
					$this->fields[$name] = new BSDate($value);
					break;
				case 'message-id':
					preg_match('/^<?([^>]*)>?$/', $value, $matches);
					$this->fields[$name] = $matches[1];
					break;
				default:
					if (strpos($value, "\n") !== 0) {
						$this->fields[$name] = BSString::explode("\n", $value);
					} else {
						$this->fields[$name] = $this->getHeader($name);
					}
					break;
			}
		}
		return $this->fields[$name];
	}

	/**
	 * 生ヘッダを返す
	 *
	 * @access public
	 * @param string $name ヘッダ名
	 * @return mixed ヘッダ
	 */
	public function getHeader ($name) {
		$name = strtolower($name);
		return $this->getHeaders()->getParameter($name);
	}

	/**
	 * 生ヘッダを全て返す
	 *
	 * @access public
	 * @return BSArray 全てのヘッダ
	 */
	public function getHeaders () {
		if (!$this->headers) {
			$this->socket->putLine('TOP ' . $this->getID() . ' 0');
			if (!$this->socket->isSuccess()) {
				throw new BSMailException(
					'ヘッダの取得に失敗しました。(%s)',
					$this->socket->getPrevLine()
				);
			}

			$this->headers = new BSArray;
			$key = null;
			foreach ($this->socket->getLines() as $line) {
				if (BSString::isBlank($line)) {
					break;
				} else if (preg_match('/^([a-z0-9\\-]+): (.*)$/i', $line, $matches)) {
					$key = strtolower($matches[1]);
					if (BSString::isBlank($this->headers[$key])) {
						$this->headers[$key] = $matches[2];
					} else {
						$this->headers[$key] .= "\n" . $matches[2];
					}
				} else if (preg_match('/^[\\t ]+(.*)$/', $line, $matches)) {
					if (BSString::getEncoding($this->headers[$key]) == 'ascii') {
						$this->headers[$key] .= ' ';
					}
					$this->headers[$key] .= $matches[1];
				}
			}
		}
		return $this->headers;
	}
}

/* vim:set tabstop=4: */
