<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * コンソール認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSConsoleSecurityFilter extends BSFilter {
	private function getRealUser () {
		return ltrim($this->controller->getAttribute('USER'), '_');
	}

	public function execute () {
		if (($user = $this->getRealUser()) != BSProcess::getCurrentUser()) {
			$message = new BSStringFormat('実行ユーザー "%s" が正しくありません。');
			$message[] = $user;
			throw new BSConsoleException($message);
		}
		if (PHP_SAPI != 'cli') {
			return BSController::COMPLETED;
		}
	}
}

