<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * クレデンシャル認証 BasicSecurityFilterとほぼ同機能
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
		if (!$credential = $action->getCredential()) {
			// filters.ini の param.credential 値も参照
			$credential = $this->getParameter('credential');
		}

		if (!$this->user->isAuthenticated()) {
			return $this->controller->forward(MO_LOGIN_MODULE, MO_LOGIN_ACTION);
		} else if ($credential && !$this->user->hasCredential($credential)) {
			$e = new BSException('クレデンシャル"%s"がありません。', $credential);
			$e->sendAlert();
			return $this->controller->forward(MO_SECURE_MODULE, MO_SECURE_ACTION);
		}

		$filters->execute();
	}
}

/* vim:set tabstop=4 ai: */
?>