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
			if (BSProcess::isExists($info['pid'])) {
				$this->request->setAttribute('pid', $info['pid']);
				$this->request->setAttribute('port', $info['port']);
			} else {
				$this->controller->removeAttribute('BSXMPPBotDaemon');
			}
		}
		$this->request->setAttribute('from', BSAuthor::getJabberID());
		$this->request->setAttribute('to', BSAdministrator::getJabberID());

		return BSView::SUCCESS;
	}

	public function validate () {
		if (!BSAuthor::getJabberID()) {
			$this->request->setError('author_jid', '未定義です。');
		}
		if (!BSAdministrator::getJabberID()) {
			$this->request->setError('admin_jid', '未定義です。');
		}
		return parent::validate();
	}
}

/* vim:set tabstop=4: */
