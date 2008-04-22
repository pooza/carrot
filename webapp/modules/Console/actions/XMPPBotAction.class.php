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

		BSLog::put(get_class($this) . 'を実行しました。');
		return BSView::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>