<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage mail
 */

/**
 * 添付メールに対応したメール送信
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSMTP extends BSSocket {
	private $from;
	private $to;
	private $headers = array();
	private $body;
	private $parts = array();
	private $messageID;
	private $boundary;
	private $keywords = array();
	const HOST = BS_SMTP_HOST;
	const TEST_MODE = true;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSHost $path ホスト
	 * @param integer $port ポート
	 */
	function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(self::HOST);
		}

		parent::__construct($host, $port);
		$this->setPriority(3);
		$this->setSubject('untitled');
		$this->setHeader('Mime-Version', '1.0');
		$this->setHeader('Content-Transfer-Encoding', '7bit');
		$this->setHeader('X-Mailer', BSController::getFullName('en'));
		$this->setFrom(BSAuthor::getMailAddress());
		$this->setTo(BSAdministrator::getMailAddress());
	}

	/**
	 * ストリームを開く
	 *
	 * @access public
	 */
	public function open () {
		if (!BSController::getInstance()->isResolvable()) {
			return;
		}

		parent::open();
		$this->putLine('EHLO ' . BSController::getInstance()->getServerHost()->getName());
		$code = $this->getResultCode();
		if (!in_array($code, array(220, 250))) {
			throw new BSMailException('%sに接続出来ません。 (%d)', $this, $code);
		}
		$this->keywords = $this->getLines();
	}

	/**
	 * ストリームを閉じる
	 *
	 * @access public
	 */
	public function close () {
		if (!BSController::getInstance()->isResolvable()) {
			return;
		}

		$this->putLine('QUIT');
		$code = $this->getResultCode();
		if ($code != 221) {
			throw new BSMailException('%sからの切断に失敗しました。 (%d)', $this, $code);
		}
		parent::close();
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param boolean $mode テストモード
	 */
	public function send ($mode = false) {
		if (!BSController::getInstance()->isResolvable()) {
			return;
		}

		for ($i = 0 ; $i < self::RETRY_LIMIT ; $i ++) {
			try {
				$this->clearMessageID();
				$this->setHeader('Message-ID', '<' . $this->getMessageID() . '>');
				$this->setHeader('Date', BSDate::getNow('r'));
				$this->putMailFromRequest();
				$this->putRcptToRequest($mode);
				$this->putDataRequest();

				$message = sprintf(
					'"%s"宛のメール"%s"を送信しました。',
					$this->to->format(BSMailAddress::NO_ENCODE),
					$this->getMessageID()
				);
				BSLog::put($message, 'Mail');
				$this->clearBoundary();
				return;
			} catch (BSMailException $e) {
				sleep(1);
			}
		}
		throw new BSMailException('%sへの送信に失敗しました。', $this->to->format());
	}

	/**
	 * MAIL FROMリクエスト
	 *
	 * @access protected
	 */
	protected function putMailFromRequest () {
		$this->putLine('MAIL FROM:' . $this->from->getAddress());
		$code = $this->getResultCode();
		if ($code != 250) {
			throw new BSMailException(
				'送信アドレス"%s"が拒否されました。(%d)',
				$this->from->getAddress(),
				$code
			);
		}
	}

	/**
	 * RCPT TOリクエスト
	 *
	 * @access protected
	 * @param boolean $mode テストモードならTrue
	 */
	protected function putRcptToRequest ($mode = false) {
		$addresses = array();
		if (BSController::getInstance()->isDebugMode() || $mode) {
			$addresses[] = BSAdministrator::EMAIL;
		} else {
			$addresses[] = $this->to->getAddress();
			foreach (BSMailAddress::getBCCAddresses() as $address) {
				$addresses[] = $address->getAddress(); 
			}
		}

		foreach ($addresses as $address) {
			$this->putLine('RCPT TO:' . $address);
			$code = $this->getResultCode();
			if (!in_array($code, array(250, 251))) {
				throw new BSMailException(
					'受信アドレス"%s"が拒否されました。(%d)',
					$address,
					$code
				);
			}
		}
	}

	/**
	 * DATAリクエスト
	 *
	 * @access protected
	 */
	protected function putDataRequest () {
		$this->putLine('DATA');
		$code = $this->getResultCode();
		if ($code != 354) {
			throw new BSMailException('DATAリクエストが拒否されました。(%d)', $code);
		}

		foreach ($this->getHeaders() as $key => $value) {
			$this->putLine($key . ': ' . $value);
		}
		$this->putLine();
		$this->putLine($this->getBody());
		$this->putLine('.');
		$code = $this->getResultCode();
		if ($code != 250) {
			throw new BSMailException('本文が拒否されました。(%d)', $code);
		}
	}

	/**
	 * ヘッダを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string ヘッダ
	 */
	public function getHeader ($name) {
		if (isset($this->headers[$name])) {
			return $this->headers[$name];
		}
	}

	/**
	 * ヘッダを設定する
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 */
	public function setHeader ($name, $value) {
		if (ereg('[[:cntrl:]]', $value)) {
			throw new BSMailException('"%s"にコントロールコードが含まれています。', $name);
		}
		$this->headers[$name] = $value;
	}

	/**
	 * ヘッダ一式を返す
	 *
	 * @access public
	 * @return string[] ヘッダ一式
	 */
	public function getHeaders () {
		return $this->headers;
	}

	/**
	 * キーワードを返す
	 *
	 * @access public
	 * @return string[] キーワード一式
	 */
	public function getKeywords () {
		return $this->keywords;
	}

	/**
	 * Subjectを設定する
	 *
	 * @access public
	 * @param string $subject Subject
	 */
	public function setSubject ($subject) {
		$subject = self::base64Encode($subject);
		if (BSController::getInstance()->isDebugMode()) {
			$subject = '[TEST] ' . $subject;
		}
		$this->setHeader('Subject', $subject);
	}

	/**
	 * X-Priorityヘッダを設定する
	 *
	 * @access public
	 * @param integer $priority X-Priorityヘッダ
	 */
	public function setPriority ($priority) {
		$this->setHeader('X-Priority', $priority);
	}

	/**
	 * 送信者を設定する
	 *
	 * @access public
	 * @param BSMailAddress $from 送信者
	 */
	public function setFrom (BSMailAddress $from) {
		$this->from = $from;
		$this->setHeader('From', $from->format());
	}

	/**
	 * 宛先を設定する
	 *
	 * @access public
	 * @param BSMailAddress $to 宛先
	 */
	public function setTo (BSMailAddress $to) {
		$this->to = $to;
		$this->setHeader('To', $to->format());
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
				BSNumeric::getRandom(),
				$this->getHost()->getName()
			);
		}
		return $this->messageID;
	}

	/**
	 * メッセージIDを初期化する
	 *
	 * @access private
	 */
	private function clearMessageID () {
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
			$this->boundary = BSCrypt::getMD5('Carrot-' . BSDate::getNow('YmdHis'));
		}
		return $this->boundary;
	}

	/**
	 * バウンダリを初期化する
	 *
	 * @access private
	 */
	private function clearBoundary () {
		$this->boundary = null;
	}

	/**
	 * 本文を返す
	 *
	 * @access public
	 * @param string $body 本文
	 */
	public function getBody () {
		if (!$this->body && $this->parts) {
			$body = array();
			foreach ($this->parts as $part) {
				$body[] = '--' . $this->getBoundary();
				foreach ($part['headers'] as $key => $value) {
					$body[] = $key . ': ' . $value;
				}
				$body[] = null;
				$body[] = $part['body'];
			}
			$body[] = '--' . $this->getBoundary() . '--';
			$this->body = implode(self::LINE_SEPARATOR, $body);
		}
		return $this->body;
	}

	/**
	 * 本文を設定
	 *
	 * @access public
	 * @param string $body 本文
	 * @param string $type メディアタイプ
	 */
	public function setBody ($body, $type = 'text/plain; charset=iso-2022-jp') {
		$body = BSString::convertKana($body, 'KV');
		$body .= "\n"; //文末に改行追加（AppleMail対応）
		if (preg_match('/^text\/plain/', $type)) {
			$body = BSString::split($body, 78);
		}
		$body = BSString::convertEncoding($body, 'iso-2022-jp');
		$body = str_replace("\n", self::LINE_SEPARATOR, $body);

		if ($this->parts) {
			// マルチパートメールの場合は、最初のパートを本文とする
			$this->body = null;
			$this->parts[0] = array(
				'headers' => array(
					'Content-Transfer-Encoding' => '7bit',
					'Content-Type' => $type,
				),
				'body' => $body,
			);
			$this->setHeader(
				'Content-Type',
				'multipart/mixed; boundary=' . $this->getBoundary()
			);
		} else {
			$this->setHeader('Content-Type', $type);
			$this->body = $body;
		}
	}

	/**
	 * 添付ファイルを追加
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @param string $body 本文
	 * @param string $type メディアタイプ
	 */
	public function addAttachment ($name, $body, $type = null) {
		if (!$this->parts) {
			// 初回の添付時、本文の内容を先頭パートに移動する
			$this->parts[0] = array('headers' => array(), 'body' => null);
			$this->setBody($this->body);
		}

		if (!$type) {
			$file = new BSFile($name);
			$type = $file->getType();
		}

		$this->parts[] = array(
			'headers' => array(
				'Content-Type' => $type,
				'Content-Transfer-Encoding' => 'base64',
				'Content-Disposition' => sprintf(
					'attachment; filename="%s"',
					self::base64Encode($name)
				),
			),
			'body' => BSString::split(base64_encode($body)),
		);
	}

	/**
	 * リザルトコードを返す
	 *
	 * @access protected
	 * @return integer リザルトコード
	 */
	protected function getResultCode () {
		if (preg_match('/^([0-9]+)/', $this->getLine(), $matches)) {
			return (integer)$matches[1];
		}
	}

	/**
	 * 文字列をbase64エンコード
	 *
	 * @access public
	 * @return string MIME'B'エンコードされた文字列
	 * @static
	 */
	public static function base64Encode ($str) {
		if (BSString::getEncoding($str) == 'ascii') {
			return $str;
		}

		$str = BSString::convertKana($str, 'KV');
		$str = BSString::convertEncoding($str, 'iso-2022-jp');
		$str = base64_encode($str);
		return '=?iso-2022-jp?B?' . $str . '?=';
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf(
			'SMTP接続 "%s:%d"',
			$this->getHost()->getName(),
			$this->getPort()
		);
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 * @static
	 */
	public static function getDefaultPort () {
		return BSServiceList::getPort('smtp');
	}
}

/* vim:set tabstop=4 ai: */
?>