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
class BSPOP3Mail {
	private $id;
	private $size;
	private $socket;
	private $headers;
	private $fields;
	private $body;

	/**
	 * @access public
	 * @param BSPOP3 $socket ソケット
	 * @param string $line レスポンス行
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
	 * @return mixed フィールドの内容
	 */
	public function getField ($name) {
		if (!$this->fields->hasParameter($name)) {
			if (BSString::isBlank($value = $this->getHeader($name))) {
				return null;
			}
			$value = BSMailUtility::decodeHeader($value);
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
					if (strpos($value, "\n") !== false) {
						$this->fields[$name] = BSString::explode("\n", $value);
					} else {
						$this->fields[$name] = $value;
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
			if (!$this->socket->execute('TOP ' . $this->getID() . ' 0')) {
				throw new BSMailException(
					'ヘッダの取得に失敗しました。(%s)',
					$this->socket->getPrevLine()
				);
			}
			$this->parseHeaders(new BSArray($this->socket->getLines()));
		}
		return $this->headers;
	}

	/**
	 * ヘッダをパース
	 *
	 * 原則的に、デコードなどは一切行わない。
	 * 但し、複数行にわたるヘッダの場合のみ、行末スペースの扱いを制御する。
	 *
	 * @access protected
	 * @param BSArray $lines ヘッダを含んだ行の配列
	 */
	protected function parseHeaders (BSArray $lines) {
		$this->headers = new BSArray;
		$key = null;
		foreach ($lines as $line) {
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
				$encoded = BSMailUtility::decodeHeader($this->headers[$key]);
				if (BSString::getEncoding($encoded) == 'ascii') {
					$this->headers[$key] .= ' ';
				}
				$this->headers[$key] .= $matches[1];
			}
		}
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
		if (!$this->body) {
			if (!$this->socket->execute('RETR ' . $this->getID())) {
				throw new BSMailException(
					'本文の取得に失敗しました。(%s)',
					$this->socket->getPrevLine()
				);
			}
			$body = new BSArray($this->socket->getLines());
			$body = $body->join("\n");
			$body = BSString::explode("\n\n", $body);

			if (!$this->headers) {
				$headers = BSString::explode("\n", $body[0]);
				$this->parseHeaders($headers);
			}

			$body->removeParameter(0);
			$body = $body->join("\n");
			$body = preg_replace('/\.$/', '', $body);
			$body = BSString::convertEncoding($body);
			$this->body = $body;
		}
		return $this->body;
	}

	/**
	 * サーバから削除
	 *
	 * @access public
	 */
	public function delete () {
		if (!$this->socket->execute('DELE ' . $this->getID())) {
			throw new BSMailException('削除に失敗しました。(%s)', $this->socket->getPrevLine());
		}

		BSController::getInstance()->putLog(
			sprintf('メール "%s" をサーバから削除しました。', $this->getMessageID()),
			get_class($this)
		);
	}
}

/* vim:set tabstop=4: */
