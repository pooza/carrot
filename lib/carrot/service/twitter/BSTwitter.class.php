<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.twitter
 */

/**
 * Twitterクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSTwitter extends BSCurlHTTP {
	private $uid;
	private $password;
	const DEFAULT_HOST = 'twitter.com';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSHost $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(self::DEFAULT_HOST);
		}
		parent::__construct($host, $port);
		$this->setAttribute('httpauth', CURLAUTH_BASIC);
		$this->setAttribute('httpheader', $this->getRequestHeaders());
	}

	/**
	 * ユーザーIDを返す
	 *
	 * @access public
	 * @return string ユーザーID
	 */
	public function getUserID () {
		return $this->uid;
	}

	/**
	 * ユーザーIDを設定する
	 *
	 * @access public
	 * @param string $id ユーザーID又はメールアドレス
	 */
	public function setUserID ($id) {
		$this->uid = $id;
	}

	/**
	 * パスワードを返す
	 *
	 * @access public
	 * @return string パスワード
	 */
	public function getPassword () {
		return $this->password;
	}

	/**
	 * パスワードを設定する
	 *
	 * @access public
	 * @param string $password パスワード
	 */
	public function setPassword ($password) {
		$this->password = $password;
	}

	/**
	 * 最新ステータスを返す
	 *
	 * @access public
	 * @return BSTwitterStatus ステータス
	 */
	public function getStatus () {
		$contents = $this->sendGetRequest('/statuses/user_timeline.xml');
		$xml = new BSXMLDocument;
		$xml->setContents($contents);
		return new BSTwitterStatus($xml->getElement('status'));
	}

	/**
	 * ステータスを設定する
	 *
	 * @access public
	 * @param string $status ステータス
	 */
	public function setStatus ($status) {
		$this->sendPostRequest('/statuses/update.xml', array('status' => $status));
	}

	/**
	 * GETリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return string レスポンスの本文
	 */
	public function sendGetRequest ($path) {
		if (!$this->getUserID()) {
			throw new BSTwitterException('ユーザーID又はメールアドレスが未定義です。');
		} else if (!$this->getPassword()) {
			throw new BSTwitterException('パスワードが未定義です。');
		}

		try {
			$this->setAttribute('userpwd', $this->getUserID() . ':' . $this->getPassword());
			return parent::sendGetRequest($path);
		} catch (BSHTTPException $e) {
			throw new BSTwitterException('認証エラーが発生した為、%sが実行出来ません。', $path);
		}
	}

	/**
	 * POSTリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param string[] $params パラメータの配列
	 * @return string レスポンスの本文
	 */
	public function sendPostRequest ($path, $params = array()) {
		if (!$this->getUserID()) {
			throw new BSTwitterException('ユーザーID又はメールアドレスが未定義です。');
		} else if (!$this->getPassword()) {
			throw new BSTwitterException('パスワードが未定義です。');
		}

		try {
			$this->setAttribute('userpwd', $this->getUserID() . ':' . $this->getPassword());
			return parent::sendPostRequest($path, $params);
		} catch (BSHTTPException $e) {
			throw new BSTwitterException('認証エラーが発生した為、%sが実行出来ません。', $path);
		}
	}

	/**
	 * 追加分リクエストヘッダを返す
	 *
	 * @access private
	 * @return string[] 追加分リクエストヘッダ
	 */
	private function getRequestHeaders () {
		return array(
			'X-Twitter-Client' => BSController::getName('en'),
		);
	}
}

/* vim:set tabstop=4 ai: */
?>