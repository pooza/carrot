<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime
 */

/**
 * MIMEメール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMIMEMail extends BSMIMEPart implements BSRenderer {
	private $from;
	private $to;
	private $cc;
	private $bcc;
	private $body;
	private $parts;
	private $contents;
	private $messageID;
	private $boundary;
	private $error;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->to = new BSArray;
		$this->cc = new BSArray;
		$this->bcc = new BSArray;

		$this->setSubject('untitled');
		$this->setHeader('Message-ID', '<' . $this->getMessageID() . '>');
		$this->setHeader('Date', BSDate::getNow('r'));
		$this->setHeader('Mime-Version', '1.0');
		$this->setHeader('X-Mailer', BSController::getFullName('en'));
		$this->setPriority(3);
		$this->setFrom(BSAuthor::getMailAddress());
		$this->setTo(BSAdministrator::getMailAddress());
	}

	/**
	 * ヘッダを設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 */
	public function setHeader ($name, $value) {
		parent::setHeader($name, $value);
		$this->contents = null;
	}

	/**
	 * レンダラーを返す
	 *
	 * @access public
	 * @return BSRenderer レンダラー
	 */
	public function getRenderer () {
		throw new BSMailException('BSMIMEMail::getRendererは利用できません。');
	}

	/**
	 * レンダラーを設定
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 */
	public function setRenderer (BSRenderer $renderer) {
		throw new BSMailException('BSMIMEMail::setRendererは利用できません。');
	}

	/**
	 * 標題を設定
	 *
	 * @access public
	 * @param string $subject Subject
	 */
	public function setSubject ($subject) {
		if (BS_DEBUG) {
			$subject = '[TEST] ' . $subject;
		}
		$this->setHeader('Subject', $subject);
	}

	/**
	 * 優先順位を設定
	 *
	 * @access public
	 * @param integer $priority 優先順位
	 */
	public function setPriority ($priority) {
		if (!in_array($priority, range(1, 5))) {
			throw new BSMailException('優先順位"%d"が正しくありません。', $priority);
		}
		$this->setHeader('X-Priority', $priority);
	}

	/**
	 * 全ての宛先を返す
	 *
	 * @access public
	 * @param BSMailAddress $email 送信者
	 */
	public function getReceipts () {
		$receipts = new BSArray;
		foreach (array('to', 'cc', 'bcc') as $key) {
			foreach ($this->$key as $email) {
				$recipients[$email->getContents()] = $email;
			}
		}
		return $receipts;
	}

	/**
	 * 送信者を設定
	 *
	 * @access public
	 * @param BSMailAddress $email 送信者
	 */
	public function setFrom (BSMailAddress $email) {
		$this->from = $email;
		$this->setHeader('From', $email->format());
	}

	/**
	 * 宛先を設定
	 *
	 * @access public
	 * @param BSMailAddress $email 宛先
	 */
	public function setTo (BSMailAddress $email) {
		$this->to = $this->to->clearParameters();
		$this->addTo($email);
	}

	/**
	 * 宛先を追加
	 *
	 * @access public
	 * @param BSMailAddress $email 宛先
	 */
	public function addTo (BSMailAddress $email) {
		$this->to[] = $email;
		$addresses = new BSArray;
		foreach ($this->to as $email) {
			$addresses[] = $email->format();
		}
		$this->setHeader('To', $addresses->join('; '));
	}

	/**
	 * CCを加える
	 *
	 * @access public
	 * @param BSMailAddress $email 宛先
	 */
	public function addCC (BSMailAddress $email) {
		$this->cc[] = $email;
		$addresses = new BSArray;
		foreach ($this->cc as $email) {
			$addresses[] = $email->format();
		}
		$this->setHeader('Cc', $addresses->join('; '));
	}

	/**
	 * CCをクリア
	 *
	 * @access public
	 */
	public function clearCC () {
		$this->cc->clearParameters();
		$this->getHeaders()->removeParameter('cc');
	}

	/**
	 * BCCを加える
	 *
	 * @access public
	 * @param BSMailAddress $email 宛先
	 */
	public function addBCC (BSMailAddress $email) {
		$this->bcc[] = $email;
	}

	/**
	 * BCCをクリア
	 *
	 * @access public
	 */
	public function clearBCC () {
		$this->bcc->clearParameters();
	}

	/**
	 * メッセージIDを返す
	 *
	 * @access public
	 * @return string メッセージID
	 */
	public function getMessageID () {
		if (!$this->messageID) {
			$this->messageID = sprintf(
				'%s.%s@%s',
				BSDate::getNow('YmdHis'),
				BSUtility::getUniqueID(),
				BS_SMTP_HOST
			);
		}
		return $this->messageID;
	}

	/**
	 * メッセージIDをクリア
	 *
	 * @access public
	 */
	public function clearMessageID () {
		$this->messageID = null;
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
	 * 全てのパートを返す
	 *
	 * @access public
	 * @return BSArray 全てのパート
	 */
	public function getParts () {
		if (!$this->parts) {
			$this->parts = new BSArray;
		}
		return $this->parts;
	}

	/**
	 * メインパートを返す
	 *
	 * @access public
	 * @return BSMIMEPart メインパート
	 */
	public function getMainPart () {
		if (!$this->getParts()->count()) {
			$renderer = new BSPlainTextRenderer;
			$renderer->setEncoding('iso-2022-jp');
			$renderer->setWidth(78);
			$renderer->setLineSeparator(self::LINE_SEPARATOR);
			$this->addAttachment($renderer);
		}
		return $this->getParts()->getParameter(0);
	}

	/**
	 * メインパートを設定
	 *
	 * @access public
	 * @param BSMIMEPart $part メインパート
	 */
	public function setMainPart (BSMIMEPart $part) {
		$this->getParts()->setParameter(0, $part);
	}

	/**
	 * マルチパートか？
	 *
	 * @access public
	 * @return boolean マルチパートならばTrue
	 */
	public function isMultiPart () {
		return (1 < $this->getParts()->count());
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
				$this->setHeader($name, $part->getHeader($name));
			}
		}

		return $part;
	}

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		if (!$this->contents) {
			foreach ($this->getHeaders() as $key => $value) {
				$this->contents .= $this->encodeHeader($key, $value);
			}
			$this->contents .= self::LINE_SEPARATOR;
			$this->contents .= $this->getBody();
		}
		return $this->contents;
	}

	/**
	 * 出力内容を設定
	 *
	 * @param string $contents 出力内容
	 * @access public
	 */
	public function setContents ($contents) {
		$this->contents = $contents;
	}

	/**
	 * 本文を返す
	 *
	 * @access public
	 * @param string $body 本文
	 */
	public function getBody () {
		if (!$this->isMultiPart()) {
			return $this->getMainPart()->getRenderer()->getContents();
		}

		if (!$this->body) {
			foreach ($this->getParts() as $part) {
				$this->body .= '--' . $this->getBoundary() . self::LINE_SEPARATOR;
				foreach ($part->getHeaders() as $key => $value) {
					$this->body .= $this->encodeHeader($key, $value);
				}
				$this->body .= self::LINE_SEPARATOR;
				if ($part->getHeader('Content-Transfer-Encoding') == 'base64') {
					$this->body .= BSString::split(
						BSMIMEUtility::encodeBase64($part->getRenderer()->getContents())
					);
				} else {
					$this->body .= $part->getRenderer()->getContents();
				}
			}
			$this->body .= '--' . $this->getBoundary() . '--';
		}
		return $this->body;
	}

	/**
	 * ヘッダをMIMEエンコードして返す
	 *
	 * @access protected
	 * @param string $key フィールド名
	 * @param string $value フィールド値
	 * @param ヘッダ行
	 */
	protected function encodeHeader ($key, $value) {
		if (strtolower($key) == 'bcc') {
			return null;
		}
		return parent::encodeHeader($key, $value);
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('.eml');
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		try {
			$receipts = $this->getReceipts();
			if (!$receipts->count()) {
				throw new BSMailException('宛先アドレスが指定されていません。');
			}
			if (BS_SMTP_CHECK_ADDRESSES) {
				foreach ($receipts as $email) {
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
}

/* vim:set tabstop=4: */
