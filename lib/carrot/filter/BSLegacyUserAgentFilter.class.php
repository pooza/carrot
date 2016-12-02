<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * 禁止されたUserAgent
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLegacyUserAgentFilter extends BSFilter {
	public function execute () {
		if ($this->request->getUserAgent()->isLegacy()) {
			$action = $this->controller->getAction('legacy_user_agent');

			//フィルタの中からはforwardできないので。
			$this->controller->registerAction($action);
		}
	}
}

/* vim:set tabstop=4: */
