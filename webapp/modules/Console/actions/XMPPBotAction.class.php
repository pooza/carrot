<?php
/**
 * XMPPBotアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class XMPPBotAction extends BSAction {
	public function execute () {
		BSXMPPBotDaemon::start();
		$this->controller->putLog('起動しました。', 'BSXMPPBotDaemon');
		return BSView::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>