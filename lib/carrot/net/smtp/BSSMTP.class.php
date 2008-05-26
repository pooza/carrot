<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.smtp
 */

/**
 * メール送信
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSMTP extends BSSocket {
	private $from;
	private $to;
	private $bcc = array();
	private $headers = array();
	private $body;
	private $parts = array();
	private $messageID;
	private $boundary;
	private $keywords = array();
	private $addresses;
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
			$host = new BSHost(BS_SMTP_HOST);
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
		if (!in_array($this->getResultCode(), array(220, 250))) {
			throw new BSMailException('%sに接続出来ません。 (%s)', $this, $this->getPrevLine());
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
		if ($this->getResultCode() != 221) {
			throw new BSMailException('%sの切断に失敗しました。(%s)',$this, $this->getPrevLine());
		}
		parent::close();
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param boolean $mode テストモード
	 * @return string 送信完了時は最終のレスポンス
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
					'"%s"宛のメール"%s"を送信しました。(%s)',
					$this->to->format(),
					$this->getMessageID(),
					$this->getPrevLine()
				);
				BSLog::put($message, 'Mail');
				$this->clearBoundary();
				return $this->getPrevLine();
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
		if ($this->getResultCode() != 250) {
			throw new BSMailException(
				'送信アドレス"%s"が拒否されました。(%s)',
				$this->from->getAddress(),
				$this->getPrevLine()
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
		$this->addresses = new BSArray;
		if (BSController::getInstance()->isDebugMode() || $mode) {
			$this->addresses[] = BSAdministrator::getMailAddress();
		} else {
			$this->addresses[] = $this->to;
			foreach ($this->bcc as $address) {
				$this->addresses[] = $address; 
			}
		}
		$this->checkAddresses();

		foreach ($this->addresses as $address) {
			$this->putLine('RCPT TO:' . $address->getContents());
			if (!in_array($this->getResultCode(), array(250, 251))) {
				throw new BSMailException(
					'受信アドレス"%s"が拒否されました。(%s)',
					$address->getContents(),
					$this->getPrevLine()
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
		if ($this->getResultCode() != 354) {
			throw new BSMailException('DATA要求が拒否されました。(%s)', $this->getPrevLine());
		}

		foreach ($this->getHeaders() as $key => $value) {
			$this->putHeader($key, $value);
		}
		$this->putLine();

		$this->putLine($this->getBody());
		$this->putLine('.');
		if ($this->getResultCode() != 250) {
			throw new BSMailException('本文が拒否されました。(%s)', $this->getPrevLine());
		}
	}

	/**
	 * エンベロープフィールドを送信
	 *
	 * @access private
	 * @param string $key フィールド名
	 * @param string $value フィールド値
	 */
	private function putHeader ($key, $value) {
		$key = BSString::capitalize($key);
		if ($key == 'Bcc') {
			return;
		}

		$value = self::base64Encode($value);
		$value = str_replace('=?iso-2022-jp?B?', "\n=?iso-2022-jp?B?", $value);
		$body = BSString::split($key . ': ' . $value);

		$init = true;
		foreach (BSString::explode("\n", $body) as $line) {
			if ($init) {
				$init = false;
			} else {
				$line = "\t" . $line;
			}
			$line = rtrim($line);
			$this->putLine($line);
		}
	}

	/**
	 * 送信前チェック
	 *
	 * @access private
	 * @return boolean 
	 */
	private function checkAddresses () {
		if (defined('BS_SMTP_CHECK_ADDRESSES') && BS_SMTP_CHECK_ADDRESSES) {
			if (!BSArray::isArray($this->addresses) || !$this->addresses->count()) {
				throw new BSMailException('宛先アドレスが指定されていません。');
				return false;
			}
			foreach ($this->addresses as $address) {
				if (!$address->isValidDomain()) {
					throw new BSMailException('%sが正しくありません。', $address);
					return false;
				}
			}
		}
		return true;
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
		if (preg_match('/[[:cntrl:]]/', $value)) {
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
	 * @param BSMailAddress $email 送信者
	 */
	public function setFrom (BSMailAddress $email) {
		$this->from = $email;
		$this->setHeader('From', $email->format());
	}

	/**
	 * 宛先を設定する
	 *
	 * @access public
	 * @param BSMailAddress $email 宛先
	 */
	public function setTo (BSMailAddress $email) {
		$this->to = $email;
		$this->setHeader('To', $email->format());
	}

	/**
	 * BCCをクリアする
	 *
	 * @access public
	 */
	public function clearBCC () {
		$this->bcc[] = array();
	}

	/**
	 * BCCを加える
	 *
	 * @access public
	 * @param BSMailAddress $bcc 宛先
	 */
	public function addBCC (BSMailAddress $bcc) {
		$this->bcc[] = $bcc;
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
	static public function base64Encode ($str) {
		if (BSString::getEncoding($str) == 'ascii') {
			return $str;
		}

		$str = BSString::convertKana($str, 'KV');
		while (preg_match('/[^[:print:]]+/', $str, $matches)) {
			$encoded = BSString::convertEncoding($matches[0], 'iso-2022-jp');
			$encoded = '=?iso-2022-jp?B?' . base64_encode($encoded) . '?=';
			$str = str_replace($matches[0], $encoded, $str);
		}
		return $str;
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
	static public function getDefaultPort () {
		return BSNetworkService::getPort('smtp');
	}
}

/* vim:set tabstop=4 ai: */
?>