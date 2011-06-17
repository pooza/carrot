<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * コンソール認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSConsoleSecurityFilter extends BSFilter {
	public function execute () {
		if (($user = BSProcess::getCurrentUser()) != BS_APP_PROCESS_UID) {
			$message = new BSStringFormat('実行ユーザー "%s" が正しくありません。');
			$message[] = $user;
			throw new BSConsoleException($message);
		}
		if (PHP_SAPI != 'cli') {
			return BSController::COMPLETED;
		}
	}
}

/* vim:set tabstop=4: */
