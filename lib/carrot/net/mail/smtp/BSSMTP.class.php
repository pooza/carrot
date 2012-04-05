<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.smtp
 */

/**
 * SMTPプロトコル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSMTP extends BSSocket {
	private $mail;
	private $keywords;
	const TEST = 1;

	/**
	 * @access public
	 * @param mixed $host ホスト
	 * @param integer $port ポート
	 * @param string $protocol プロトコル
	 *   BSNetworkService::TCP
	 *   BSNetworkService::UDP
	 */
	public function __construct ($host = null, $port = null, $protocol = BSNetworkService::TCP) {
		if (BSString::isBlank($host)) {
			$host = new BSHost(BS_SMTP_HOST);
		}
		parent::__construct($host, $port, $protocol);
		$this->setMail(new BSMail);
		$this->keywords = new BSArray;
	}

	/**
	 * ストリームを開く
	 *
	 * @access public
	 */
	public function open () {
		parent::open();
		stream_set_timeout($this->client, 0, BS_SMTP_TIMEOUT);
		$command = 'EHLO ' . BSController::getInstance()->getHost()->getName();
		if (!in_array($this->execute($command), array(220, 250))) {
			$message = new BSStringFormat('%sに接続できません。 (%s)');
			$message[] = $this;
			$message[] = $this->getPrevLine();
			throw new BSMailException($message);
		}
		while (!BSString::isBlank($line = $this->getLine())) {
			$this->keywords[] = $line;
		}
	}

	/**
	 * ストリームを閉じる
	 *
	 * @access public
	 */
	public function close () {
		if ($this->execute('QUIT') != 221) {
			$message = new BSStringFormat('%sから切断できません。(%s)');
			$message[] = $this;
			$message[] = $this->getPrevLine();
			throw new BSMailException($message);
		}
		parent::close();
	}

	/**
	 * メールを返す
	 *
	 * @access public
	 * @return BSMail メール
	 */
	public function getMail () {
		return $this->mail;
	}

	/**
	 * メールを設定
	 *
	 * @access public
	 * @param BSMail $mail メール
	 */
	public function setMail (BSMail $mail) {
		$this->mail = $mail;
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param integer $flags フラグのビット列
	 *   self::TEST テスト送信
	 * @return string 送信完了時は最終のレスポンス
	 */
	public function send ($flags = null) {
		try {
			$this->getMail()->clearMessageID();
			$this->execute('MAIL FROM:' . $this->getFrom()->getContents());
			foreach ($this->getRecipients($flags) as $email) {
				$this->execute('RCPT TO:' . $email->getContents());
			}
			$this->execute('DATA');
			$this->putLine($this->getMail()->getContents());
			if ($this->execute('.') != 250) {
				throw new BSMailException($this->getPrevLine());
			}
		} catch (BSMailException $e) {
			throw new BSMailException($this->getMail() . 'を送信できません。');
		}
		return $this->getPrevLine();
	}

	/**
	 * 送信者を返す
	 *
	 * @access protected
	 * @return BSMailAddress 送信者
	 */
	protected function getFrom () {
		return $this->getMail()->getHeader('From')->getEntity();
	}

	/**
	 * 受信者を返す
	 *
	 * @access protected
	 * @param integer $flags フラグのビット列
	 *   self::TEST テスト送信
	 * @return BSArray 受信者の配列
	 */
	protected function getRecipients ($flags = null) {
		if (BS_DEBUG || ($flags & self::TEST)) {
			$recipients = new BSArray;
			$recipients[] = BSAdministratorRole::getInstance()->getMailAddress();
			return $recipients;
		} else {
			return clone $this->getMail()->getRecipients();
		}
	}

	/**
	 * キーワードを返す
	 *
	 * @access public
	 * @return BSArray キーワード一式
	 */
	public function getKeywords () {
		if (!$this->keywords) {
			$this->keywords = new BSArray;
		}
		return $this->keywords;
	}

	/**
	 * Subjectを設定
	 *
	 * @access public
	 * @param string $subject Subject
	 */
	public function setSubject ($subject) {
		$this->getMail()->setHeader('Subject', $subject);
	}

	/**
	 * X-Priorityヘッダを設定
	 *
	 * @access public
	 * @param integer $priority X-Priorityヘッダ
	 */
	public function setPriority ($priority) {
		$this->getMail()->setHeader('X-Priority', $priority);
	}

	/**
	 * 送信者を設定
	 *
	 * @access public
	 * @param BSMailAddress $email 送信者
	 */
	public function setFrom (BSMailAddress $email) {
		$this->getMail()->setHeader('From', $email);
	}

	/**
	 * 宛先を設定
	 *
	 * @access public
	 * @param BSMailAddress $email 宛先
	 */
	public function setTo (BSMailAddress $email) {
		$this->getMail()->setHeader('To', $email);
	}

	/**
	 * BCCを加える
	 *
	 * @access public
	 * @param BSMailAddress $bcc 宛先
	 */
	public function addBCC (BSMailAddress $bcc) {
		$this->getMail()->getHeader('BCC')->appendContents($bcc);
	}

	/**
	 * 本文を返す
	 *
	 * @access public
	 * @param string $body 本文
	 */
	public function getBody () {
		return $this->getMail()->getBody();
	}

	/**
	 * 本文を設定
	 *
	 * @access public
	 * @param string $body 本文
	 */
	public function setBody ($body) {
		return $this->getMail()->setBody($body);
	}

	/**
	 * コマンドを実行し、結果を返す。
	 *
	 * @access public
	 * @param string $command コマンド
	 * @return boolean 成功ならばTrue
	 */
	public function execute ($command) {
		$this->putLine($command);
		if (!mb_ereg('^([[:digit:]]+)', $this->getLine(), $matches)) {
			$message = new BSStringFormat('不正なレスポンスです。 (%s)');
			$message[] = $this->getPrevLine();
			throw new BSMailException($message);
		}
		if (400 <= ($result = $matches[1])) {
			$message = new BSStringFormat('%s (%s)');
			$message[] = $this->getPrevLine();
			$message[] = $command;
			throw new BSMailException($message);
		}
		return $result;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('SMTPソケット "%s"', $this->getName());
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 */
	public function getDefaultPort () {
		return BSNetworkService::getPort('smtp');
	}
}

/* vim:set tabstop=4: */
