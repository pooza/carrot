<?php
/**
 * Summaryアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminTwitter
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class SummaryAction extends BSAction {
	public function execute () {
		$account = BSAuthorRole::getInstance()->getTwitterAccount();
		if ($account->isAuthenticated()) {
			$this->request->setAttribute('account', $account);
		} else {
			$values = array('url' => $account->getOAuthURL()->getContents());
			$this->request->setAttribute('oauth', $values);
		}
		return BSView::SUCCESS;
	}
}

/* vim:set tabstop=4: */
