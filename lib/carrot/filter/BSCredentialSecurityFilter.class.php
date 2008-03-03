<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * クレデンシャル認証
 *
 * BasicSecurityFilterとほぼ同機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSCredentialSecurityFilter extends SecurityFilter {

	/**
	 * プロパティ取得のオーバライド
	 *
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
				return $this->getContext()->getController();
			case 'request':
				return $this->getContext()->getRequest();
			case 'user':
				return $this->getContext()->getUser();
			case 'context':
				return $this->getContext();
		}
	}

	public function execute ($filters) {
		$action = $this->controller->getActionStack()->getLastEntry()->getActionInstance();
		if ($action->getCredential() !== null) {
			$credential = $action->getCredential();
		} else {
			$credential = $this->getParameter('credential');
		}

		if ($credential && !$this->user->hasCredential($credential)) {
			if (defined('APP_SECURE_MODULE')) {
				$module = APP_SECURE_MODULE;
			} else {
				$module = MO_SECURE_MODULE;
			}

			if (defined('APP_SECURE_ACTION')) {
				$action = APP_SECURE_ACTION;
			} else {
				$action = MO_SECURE_ACTION;
			}

			return $this->controller->forward($module, $action);
		}

		$filters->execute();
	}
}

/* vim:set tabstop=4 ai: */
?>