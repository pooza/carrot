<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * クレデンシャル認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSecurityFilter extends BSFilter {
	public function execute (FilterChain $filters) {
		if ($this->controller->getAction()->getCredential() !== null) {
			$credential = $this->controller->getAction()->getCredential();
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