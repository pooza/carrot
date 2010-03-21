<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail
 */

/**
 * メール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMail extends BSMIMEDocument {
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
		$renderer->setOptions(BSPlainTextRenderer::TAIL_LF);
		$this->setRenderer($renderer);

		$this->setHeader('Subject', 'untitled');
		$this->setHeader('Content-Type', $renderer);
		$this->setHeader('Content-Transfer-Encoding', $renderer);
		$this->setHeader('Date', BSDate::getNow());
		$this->setHeader('Mime-Version', '1.0');
		$this->setHeader('X-Mailer', null);
		$this->setHeader('X-Priority', 3);
		$this->setHeader('From', BSAuthorRole::getInstance()->getMailAddress());
		$this->setHeader('To', BSAdministratorRole::getInstance()->getMailAddress());

		if (BS_DEBUG) {
			$this->setHeader('X-Carrot-Debug-Mode', 'yes');
		}
	}

	/**
	 * メッセージIDを更新
	 *
	 * @access public
	 */
	public function updateMessageID () {
		$this->setHeader('Message-Id', null);
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
		foreach (array('To', 'Cc', 'Bcc') as $key) {
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
			foreach ($this->getRecipients() as $email) {
				if (BS_SMTP_CHECK_ADDRESSES && !$email->isValidDomain()) {
					$message = new BSStringFormat('宛先%sが正しくありません。');
					$message[] = $email;
					throw new BSMailException($message);
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
