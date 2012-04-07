<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * 禁止されたUserAgent
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLegacyUserAgentFilter extends BSFilter {
	public function initialize ($params = array()) {
		$this['module'] = 'Default';
		$this['action'] = 'DeniedUserAgent';
		return parent::initialize($params);
	}

	public function execute () {
		if ($this->request->getUserAgent()->isLegacy()) {
			try {
				$module = $this->controller->getModule($this['module']);
				$action = $module->getAction($this['action']);
			} catch (BSException $e) {
				$action = $this->controller->getAction('not_found');
			}

			//フィルタの中からはforwardできないので。
			$this->controller->registerAction($action);
		}
	}
}

/* vim:set tabstop=4: */
