<?php
/**
 * @package org.carrot-framework
 */

/**
 * 例外
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSException extends Exception {

	/**
	 * @access public
	 */
	public function __construct () {
		$this->setName(get_class($this));

		switch (count($args = func_get_args())) {
			case 0:
				$message = $this->getName() . 'が発生しました。';
				break;
			case 1:
				$message = $args[0];
				break;
			default:
				foreach ($args as &$arg) {
					$arg = '\'' . str_replace('\'', '\\\'', $arg) . '\'';
				}
				eval('$message = sprintf(' . join(',', $args) . ');');
		}

		parent::__construct($message);
		BSController::getInstance()->putLog($this->message, $this->getName());
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 名前を設定
	 *
	 * @access protected
	 * @param string $name 名前
	 */
	protected function setName ($name) {
		$this->name = $name;
	}

	/**
	 * 報告メールを管理者に送信
	 *
	 * @access public
	 */
	public function sendMail () {
		try {
			$smtp = new BSSmartySender;
			$smtp->setTemplate('BSException.mail');
			$smtp->setAttribute('exception_name', $this->getName());
			$smtp->setAttribute('clienthost', BSRequest::getInstance()->getHost()->getName());
			$smtp->setAttribute('useragent', BSRequest::getInstance()->getUserAgent()->getName());
			$smtp->setAttribute('message', $this->getMessage());
			$smtp->render();
			$smtp->send();
			$smtp->close();
		} catch (Exception $e) {
			// 送信に失敗した場合でもログだけは残る
		}
	}

	/**
	 * 報告IMを管理者に送信
	 *
	 * @access public
	 */
	public function sendAlert () {
		if (BSAdministrator::getJabberID()) {
			try {
				$xmpp = new BSXMPPBotClient(BSController::getInstance()->getHost());
				$xmpp->putLine(BSLogManager::formatMessage($this->getMessage(), $this->getName()));
			} catch (Exception $e) {
				$this->sendMail();
			}
		} else {
			$this->sendMail();
		}
	}
}

/* vim:set tabstop=4: */
