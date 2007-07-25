<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * MojaviExceptionのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSException.class.php 261 2007-01-03 13:25:18Z pooza $
 */
class BSException extends MojaviException {

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct () {
		switch (count($args = func_get_args())) {
			case 0:
				$message = '例外が発生しました。';
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
		$this->setName(get_class($this));
		BSLog::put($this->message, get_class($this));
	}

	/**
	 * 報告メールを管理者に送信
	 *
	 * @access public
	 */
	public function sendMail () {
		try {
			$controller = BSController::getInstance();
			$smtp = new BSSmartySender();
			$smtp->setSubject(
				sprintf('[%s] %s', BSController::getName(), get_class($this))
			);
			$smtp->setTemplate('BSException.mail');
			$smtp->setAttribute('clienthost', $controller->getClientHost()->getName());
			$smtp->setAttribute('useragent', $controller->getEnvironment('HTTP_USER_AGENT'));
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
	public function sendNotify () {
		try {
			$controller = BSController::getInstance();
			$xmpp = new BSXMPPBotClient($controller->getServerHost());
			$xmpp->putLine(BSLog::getMessage($this->getMessage(), get_class($this)));
		} catch (Exception $e) {
			$this->sendMail();
		}
	}
}

/* vim:set tabstop=4 ai: */
?>