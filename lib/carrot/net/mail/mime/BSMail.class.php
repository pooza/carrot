<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime
 */

/**
 * メール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMail extends BSMIMEDocument {
	private $boundary;
	private $error;

	/**
	 * @access public
	 */
	public function __construct () {
		$renderer = new BSPlainTextRenderer;
		$renderer->setEncoding('iso-2022-jp');
		$renderer->setWidth(78);
		$renderer->setConvertKanaFlag('KV');
		$renderer->setLineSeparator(self::LINE_SEPARATOR);

		$this->addAttachment($renderer);
		$this->setHeader('Subject', 'untitled');
		$this->setHeader('Message-ID', null);
		$this->setHeader('Date', BSDate::getNow());
		$this->setHeader('Mime-Version', '1.0');
		$this->setHeader('X-Mailer', null);
		$this->setHeader('X-Priority', 3);
		$this->setHeader('From', BSAuthor::getMailAddress());
		$this->setHeader('To', BSAdministrator::getMailAddress());

		if (BS_DEBUG) {
			$this->setHeader('X-Carrot-Debug-Mode', 'yes');
		}
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 */
	public function send () {
		$smtp = new BSSMTP;
		$smtp->setMail($this);
		$smtp->send();
		$smtp->close();
	}

	/**
	 * 全ての宛先を返す
	 *
	 * @access public
	 * @param BSMailAddress $email 送信者
	 */
	public function getRecipients () {
		$recipients = new BSArray;
		foreach (array('To', 'CC', 'BCC') as $key) {
			if (!$header = $this->getHeader($key)) {
				continue;
			}
			foreach ($header->getEntity() as $email) {
				$recipients[$email->getContents()] = $email;
			}
		}
		return $recipients;
	}

	/**
	 * メッセージIDを返す
	 *
	 * @access public
	 * @return string メッセージID
	 */
	public function getMessageID () {
		return $this->getHeader('Message-ID')->getEntity();
	}

	/**
	 * バウンダリを返す
	 *
	 * @access private
	 * @return string バウンダリ
	 */
	private function getBoundary () {
		if (!$this->boundary) {
			$this->boundary = BSUtility::getUniqueID();
		}
		return $this->boundary;
	}

	/**
	 * 添付ファイルを追加
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 * @param string $name ファイル名
	 * @return BSMIMEPart 追加されたパート
	 */
	public function addAttachment (BSRenderer $renderer, $name = null) {
		$part = new BSMIMEPart;
		$part->setRenderer($renderer);
		if (!BSString::isBlank($name)) {
			$part->setFileName($name, BSMIMEPart::ATTACHMENT);
		}

		$parts = $this->getParts();
		$parts[] = $part;
		$this->body = null;
		$this->contents = null;

		if ($this->isMultiPart()) {
			$this->setHeader('Content-Type', 'multipart/mixed; boundary=' . $this->getBoundary());
			$this->setHeader('Content-Transfer-Encoding', null);
		} else {
			foreach (array('Content-Type', 'Content-Transfer-Encoding') as $name) {
				$this->setHeader($name, $part->getHeader($name)->getContents());
			}
		}

		return $part;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		try {
			if (BSString::isBlank($this->getHeader('From')->getContents())) {
				throw new BSMailException('送信元アドレスが指定されていません。');
			}

			if (!$this->getRecipients()->count()) {
				throw new BSMailException('宛先アドレスが指定されていません。');
			}
			if (BS_SMTP_CHECK_ADDRESSES) {
				foreach ($this->getRecipients() as $email) {
					if (!$email->isValidDomain()) {
						throw new BSMailException('%sが正しくありません。', $address);
					}
				}
			}
			return true;
		} catch (BSMailException $e) {
			$this->error = $e->getMessage();
			return false;
		}
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('メール "%s"', $this->getMessageID());
	}
}

/* vim:set tabstop=4: */
