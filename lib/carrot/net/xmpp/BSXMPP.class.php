<?php
/**
 * @package org.carrot-framework
 * @subpackage net.xmpp
 */

BSUtility::includeFile('XMPPHP/XMPP');

/**
 * XMPPプロトコル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSXMPP extends XMPPHP_XMPP {
	private $to;

	/**
	 * @access public
	 */
	public function __construct () {
		try {
			parent::__construct(
				BS_XMPP_HOST,
				BS_XMPP_PORT,
				BS_APP_XMPP_JID,
				BSCrypt::getInstance()->decrypt(BS_APP_XMPP_PASSWORD),
				BSController::getInstance()->getName('en'),
				BS_XMPP_SERVER
			);
		} catch(XMPPHP_Exception $e) {
			throw new BSXMPPException($e->getMessage());
		}
		$this->setTo(BSAdministratorRole::getInstance()->getJabberID());
	}

	/**
	 * 宛先を返す
	 *
	 * @access public
	 * @return BSJabberID 宛先
	 */
	public function getTo () {
		return $this->to;
	}

	/**
	 * 宛先を設定
	 *
	 * @access public
	 * @param BSJabberID $jid 宛先
	 */
	public function setTo ($jid) {
		if (!($jid instanceof BSJabberID)) {
			$jid = new BSJabberID($jid);
		}
		$this->to = $jid;
	}

	/**
	 * メッセージを送信
	 *
	 * @access public
	 * @param string $message メッセージ
	 */
	public function sendMessage ($message) {
		if (!$this->to) {
			throw new BSXMPPException('宛先が指定されていません。');
		}
		try {
			if (!$this->authed) {
				$this->connect();
				$this->processUntil('session_start');
			}
			$this->message($this->to->getContents(), $message);
		} catch(XMPPHP_Exception $e) {
			throw new BSXMPPException($e->getMessage());
		}
	}
}

/* vim:set tabstop=4: */
