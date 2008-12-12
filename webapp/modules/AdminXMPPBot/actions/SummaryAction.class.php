<?php
/**
 * Summaryアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class SummaryAction extends BSAction {
	public function execute () {
		if ($info = $this->controller->getAttribute('BSXMPPBotDaemon')) {
			if (BSProcess::isExist($info['pid'])) {
				$this->request->setAttribute('pid', $info['pid']);
				$this->request->setAttribute('port', $info['port']);
			} else {
				$this->controller->removeAttribute('BSXMPPBotDaemon');
			}
		}
		$this->request->setAttribute('from', BSAuthor::getJabberID()->getContents());
		$this->request->setAttribute('to', BSAdministrator::getJabberID()->getContents());

		return BSView::SUCCESS;
	}
}

/* vim:set tabstop=4: */
