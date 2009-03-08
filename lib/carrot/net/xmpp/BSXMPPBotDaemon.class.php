<?php
/**
 * @package org.carrot-framework
 * @subpackage net.xmpp
 */

/**
 * XMPPBotデーモン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSXMPPBotDaemon extends BSDaemon {
	private $xmpp;

	/**
	 * 初期化
	 *
	 * @access protected
	 * @return boolean 正常終了ならばTrue
	 */
	protected function initialize () {
		$this->xmpp = new BSXMPP;
		return $this->xmpp->auth(
			BSAuthor::getJabberID(),
			BSController::getInstance()->getConstant('AUTHOR_PASSWORD')
		);
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
						$this->getXMPP()->send($matches[1], new BSJabberID($matches[2]));
					} else {
						$this->getXMPP()->send($line);
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
		$this->getXMPP()->setStatus();
	}

	/**
	 * アイドル処理の周期を返す
	 *
	 * @access public
	 * @return integer アイドル処理の周期を秒単位で（何もしないなら0を返す）
	 */
	public function getIdleTimeSeconds () {
		return 300;
	}

	/**
	 * XMPPサーバを返す
	 *
	 * @access private
	 * @return BSXMPP XMPPサーバ
	 */
	private function getXMPP () {
		return $this->xmpp;
	}
}

/* vim:set tabstop=4: */
