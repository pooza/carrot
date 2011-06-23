<?php
/**
 * Summaryアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminTwitter
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class SummaryAction extends BSAction {

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return 'Twitterの状態';
	}

	public function initialize () {
		parent::initialize();
		if ($errors = $this->user->getAttribute('errors')) {
			$this->request->setErrors($errors);
			$this->user->removeAttribute('errors');
		}
		return true;
	}

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
