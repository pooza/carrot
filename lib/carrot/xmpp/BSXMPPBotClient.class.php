<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * XMPPBotデーモンへの接続
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSXMPPBotClient.class.php 241 2006-12-03 08:16:03Z pooza $
 */
class BSXMPPBotClient extends BSSocket {

	/**
	 * ストリームを開く
	 *
	 * @access public
	 */
	public function open () {
		$controller = BSController::getInstance();
		if (!$info = $controller->getAttribute('BSXMPPBotDaemon')) {
			$controller->removeAttribute('BSXMPPBotDaemon');
			throw new BSXMPPException('XMPPBotデーモンが起動していません。');
		} else if (!BSProcess::isExist($info['pid'])) {
			$controller->removeAttribute('BSXMPPBotDaemon');
			throw new BSXMPPException('XMPPBotデーモンが起動していません。');
		}
		parent::open();
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 * @static
	 */
	public static function getDefaultPort () {
		if ($info = BSController::getInstance()->getAttribute('BSXMPPBotDaemon')) {
			return $info['port'];
		}
	}
}

/* vim:set tabstop=4 ai: */
?>