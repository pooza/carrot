<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * 禁止されたUserAgent
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDeniedUserAgentFilter extends BSFilter {
	public function initialize ($parameters = array()) {
		$this['module'] = 'Default';
		$this['action'] = 'DeniedUserAgent';
		return parent::initialize($parameters);
	}

	public function execute (BSFilterChain $filters) {
		if ($this->request->getUserAgent()->isDenied()) {
			try {
				$action = BSModule::getInstance($this['module'])->getAction($this['action']);
			} catch (BSException $e) {
				$action = $this->controller->getNotFoundAction();
			}
			BSActionStack::getInstance()->register($action);
		}
		$filters->execute();
	}
}

/* vim:set tabstop=4: */
