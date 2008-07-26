<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.xmpp
 */

/**
 * XMPPプロトコル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSXMPP extends BSSocket {
	private $streamID;
	private $authID;
	private $status;
	private $jid;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSHost $path ホスト
	 * @param integer $port ポート
	 */
	function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(BS_XMPP_HOST);
		}
		parent::__construct($host, $port);
	}

	/**
	 * ストリームを開く
	 *
	 * @access public
	 */
	public function open () {
		parent::open();
		$this->putLine('<?xml version="1.0" encoding="utf-8" ?>');
		$this->putLine(
			sprintf(
				'<stream:stream to="%s" xmlns="%s" xmlns:stream="%s">',
				$this->getHost()->getName(),
				'jabber:client',
				'http://etherx.jabber.org/streams'
			)
		);

		// レスポンスの末尾に閉じタグをつけて、強制的にパース
		$response = implode("\n", $this->getLines());
		$response = preg_replace('/(<\/stream:stream>)*$/', '</stream:stream>', $response);
		$element = new BSXMLDocument('stream:stream');
		$element->setContents($response);
		$this->streamID = $element->getAttribute('id');
	}

	/**
	 * ストリームを閉じる
	 *
	 * @access public
	 */
	public function close () {
		sleep(1);
		$this->putLine('</stream:stream>');
		$this->streamID = null;
		$this->authID = null;
		$this->status = null;
		$this->jid = null;
		parent::close();
	}

	/**
	 * 認証
	 *
	 * @access public
	 * @param BSJabberID $jid JabberID
	 * @param string $password パスワード
	 */
	public function auth (BSJabberID $jid, $password) {
		if ($this->jid) {
			throw new BSXMPPException('ログイン済みです。');
		} else if ($jid->getHost()->getName() != $this->getHost()->getName()) {
			throw new BSXMPPException('他サーバのJabberIDはサポートしていません。');
		}

		$query = array('username' => $jid->getAccount());
		$this->putIqRequest('get', 'jabber:iq:auth', $this->getAuthID(), $query);
		$response = $this->getResponse();

		$query = array(
			'username' => $jid->getAccount(),
			'resource' => $jid->getResource(),
		);
		if ($response->query('/iq/query/digest')) {
			// レスポンスにdigest要素が含まれるなら、ダイジェスト認証を行う
			$query['digest'] = BSCrypt::getSHA1($this->getStreamID() . $password);
		} else {
			$query['password'] = $password;
		}
		$this->putIqRequest('set', 'jabber:iq:auth', $this->getAuthID(), $query);
		$response = $this->getResponse();
		if ($response->getAttribute('id') != $this->getAuthID()) {
			throw new BSXMPPException('%sで認証エラーが発生しました。', $this);
		}

		$this->jid = $jid;
		$this->setStatus();
	}

	/**
	 * ステータスを設定
	 *
	 * @access public
	 * @param string $status ステータス
	 */
	public function setStatus ($status = 'online') {
		if (!$this->isOpened()) {
			throw new BSXMPPException('%sに接続していません。', $this);
		}

		$element = new BSXMLElement('presence');
		$element->createElement('status', $status);
		$this->putLine($element->getContents());
		$this->status = $status;
	}

	/**
	 * メッセージを送信
	 *
	 * @access public
	 * @param string $body メッセージ
	 * @param BSJabberID $to JabberID
	 */
	public function send ($body, BSJabberID $to = null) {
		if (!$to) {
			$to = BSAdministrator::getJabberID();
		} else if ($to->getHost()->getName() != $this->getHost()->getName()) {
			throw new BSXMPPException('他サーバのJabberIDはサポートしていません。');
		}

		$body = BSString::convertEncoding($body, 'utf-8');
		$body = BSString::sanitize($body);

		$element = new BSXMLElement('message');
		$element->setAttribute('to', $to->getContents());
		$element->setAttribute('type', 'normal');
		$element->setAttribute('id', $this->getMessageID());
		$element->createElement('body', $body);

		$this->putLine($element->getContents());
	}

	/**
	 * セッションのストリームIDを返す
	 *
	 * @access private
	 * @return string ストリームID
	 */
	private function getStreamID () {
		if (!$this->streamID) {
			throw new BSXMPPException('%sのストリームIDが未定義です。', $this);
		}
		return $this->streamID;
	}

	/**
	 * 認証IDを返す
	 *
	 * @access private
	 * @return string 認証ID
	 */
	private function getAuthID () {
		if (!$this->authID) {
			$signature = sprintf(
				'%s-%s',
				BSDate::getNow('YmdHis'),
				BSController::getInstance()->getClientHost()->getName()
			);
			$this->authID = 'auth_' . BSCrypt::getMD5($signature);
		}
		return $this->authID;
	}

	/**
	 * メッセージIDを返す
	 *
	 * @access private
	 * @return string メッセージID
	 */
	private function getMessageID () {
		return sprintf(
			'%s.%s@%s',
			BSDate::getNow('YmdHis'),
			BSNumeric::getRandom(),
			$this->getHost()->getName()
		);
	}

	/**
	 * レスポンスを返す
	 *
	 * @access public
	 * @param string $name 要素の名前
	 * @return string レスポンス
	 */
	public function getResponse ($name = null) {
		$element = new BSXMLElement($name);
		$element->setContents(implode("\n", $this->getLines()));
		return $element;
	}

	/**
	 * Iqリクエストを送信
	 *
	 * @access public
	 * @param string $type タイプ(get|set)
	 * @param string $xmlns ネームスペース
	 * @param string $id ID
	 * @param string[] $parameters query要素の内容
	 */
	public function putIqRequest ($type, $xmlns, $id, $parameters = array()) {
		$request = new BSXMLElement('iq');
		$request->setAttribute('type', $type);
		$request->setAttribute('id', $id);
		$query = $request->createElement('query');
		$query->setAttribute('xmlns', $xmlns);

		foreach ($parameters as $key => $value) {
			$query->createElement($key, $value);
		}

		$this->putLine($request->getContents());
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf(
			'XMPP接続 "%s:%d"',
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
		foreach (array('xmpp-client', 'jabber-client') as $service) {
			if ($port = BSNetworkService::getPort($service)) {
				return $port;
			}
		}
		return 5222;
	}
}

/* vim:set tabstop=4 ai: */
?>