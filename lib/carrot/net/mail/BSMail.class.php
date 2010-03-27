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
	static private $smtp;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->setRenderer($this->getDefaultRenderer());
		$this->setHeader('Subject', 'untitled');
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
	public function clearMessageID () {
		$this->setHeader('Message-Id', null);
	}

	/**
	 * 既定レンダラーを返す
	 *
	 * @access protected
	 * @return BSRenderer 既定レンダラー
	 */
	protected function getDefaultRenderer () {
		$renderer = new BSPlainTextRenderer;
		$renderer->setEncoding('iso-2022-jp');
		$renderer->setWidth(78);
		$renderer->setConvertKanaFlag('KV');
		$renderer->setLineSeparator(self::LINE_SEPARATOR);
		$renderer->setOptions(BSPlainTextRenderer::TAIL_LF);
		return $renderer;
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 */
	public function send () {
		$smtp = self::getServer();
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
			if ($header = $this->getHeader($key)) {
				foreach ($header->getEntity() as $email) {
					$recipients[$email->getContents()] = $email;
				}
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

	/**
	 * SMTPサーバを返す
	 * 
	 * @access public
	 * @return BSSMTP SMTPサーバ
	 * @static
	 */
	static public function getServer () {
		if (!self::$smtp) {
			self::$smtp = new BSSMTP;
		}
		return self::$smtp;
	}

	/**
	 * 送信可能か？
	 * 
	 * @access public
	 * @return boolean 送信可能ならTrue
	 * @static
	 */
	static public function isEnable () {
		try {
			self::getServer();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
}

/* vim:set tabstop=4: */
