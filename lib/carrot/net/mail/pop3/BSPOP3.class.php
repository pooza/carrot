<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.pop3
 */

/**
 * メール受信
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPOP3 extends BSSocket {
	private $mails;

	/**
	 * ストリームを開く
	 *
	 * @access public
	 */
	public function open () {
		parent::open();
		if (!$this->isSuccess()) {
			throw new BSMailException('%sに接続出来ません。 (%s)', $this, $this->getPrevLine());
		}
	}

	/**
	 * ストリームを閉じる
	 *
	 * @access public
	 */
	public function close () {
		$this->putLine('QUIT');
		if (!$this->isSuccess()) {
			throw new BSMailException('%sの切断に失敗しました。(%s)',$this, $this->getPrevLine());
		}
		parent::close();
	}

	/**
	 * 認証
	 *
	 * @access public
	 * @param string $user ユーザー名
	 * @param string $password パスワード
	 * @return boolean 認証の正否
	 */
	public function auth ($user, $password) {
		$this->putLine('USER ' . $user);
		if (!$this->isSuccess()) {
			return false;
		}
		$this->putLine('PASS ' . $password);
		if (!$this->isSuccess()) {
			return false;
		}
		return true;
	}

	/**
	 * サーバに残っているメールを全て返す
	 *
	 * @access public
	 * @return BSArray 全てのメール
	 */
	public function getMails () {
		if (!$this->mails) {
			$this->mails = new BSArray;
			if (!$this->isOpened()) {
				$this->open();
			}

			$this->putLine('LIST');
			if (!$this->isSuccess()) {
				throw new BSMailException(
					'メール一覧の取得に失敗しました。(%s)',
					$this->getPrevLine()
				);
			}

			foreach ($this->getLines() as $line) {
				if ($line == '.') {
					break;
				}
				$mail = new BSPOP3Mail($this, $line);
				$this->mails[$mail->getID()] = $mail;
			}
		}
		return $this->mails;
	}

	/**
	 * サーバに残っているメールを返す
	 *
	 * @access public
	 * @param integer $id メールの番号
	 * @return BSPOP3Mail メール
	 */
	public function getMail ($id) {
		return $this->getMails()->getParameter($id);
	}

	/**
	 * 直前のコマンドは実行に成功したか？
	 *
	 * @access public
	 * @return boolean 成功ならばTrue
	 */
	public function isSuccess () {
		return preg_match('/^\+OK/', $this->getLine());
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf(
			'POP3サーバ "%s:%d"',
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
		return BSNetworkService::getPort('pop3');
	}
}

/* vim:set tabstop=4: */
