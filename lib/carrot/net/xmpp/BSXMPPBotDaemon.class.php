<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * XMPPBotデーモン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSXMPPBotDaemon extends BSDaemon {
	static private $xmpp;

	/**
	 * デーモン開始
	 *
	 * @access public
	 * @static
	 */
	static public function start () {
		do {
			$status = self::initialize(__CLASS__);
			$listener = Nanoserv::New_Listener(
				'tcp://0.0.0.0:' . $status['port'],
				__CLASS__
			);
		} while (!$listener->Activate());
		self::getXMPP();

		$message = sprintf(
			'%s（ポート:%d, PID:%d）を開始しました。',
			__CLASS__,
			$status['port'],
			$status['pid']
		);
		BSLog::put($message);

		Nanoserv::Run();
	}

	/**
	 * 受信時
	 *
	 * @access public
	 * @param string $line 受信文字列
	 */
	public function onGetLine ($line) {
		switch ($line) {
			case null;
				break;
			case '/QUIT':
			case '/EXIT':
				$this->disconnect();
				exit;
			default:
				try {
					if (preg_match('/^(.*)\t(.*)$/', $line, $matches)) {
						self::getXMPP()->send($matches[1], new BSJabberID($matches[2]));
					} else {
						self::getXMPP()->send($line);
					}
				} catch (Exception $e) {
				}
				break;
		}
	}

	/**
	 * アイドル時
	 *
	 * @access public
	 */
	public function onIdle () {
		self::getXMPP()->setStatus();
	}

	/**
	 * XMPPサーバを返す
	 *
	 * @access private
	 * @return BSXMPP XMPPサーバ
	 * @static
	 */
	static private function getXMPP () {
		if (!self::$xmpp) {
			self::$xmpp = new BSXMPP;
			self::$xmpp->auth(BSAuthor::getJabberID(), BS_AUTHOR_PASSWORD);
		}
		return self::$xmpp;
	}
}

/* vim:set tabstop=4 ai: */
?>