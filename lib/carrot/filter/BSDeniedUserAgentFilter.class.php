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
class BSDeniedUserAgentFilter extends BSFilter {
	public function initialize ($params = array()) {
		if (BS_DEBUG || $this->user->isAdministrator()) {
			if (!BSString::isBlank($name = $this[BSUserAgent::ACCESSOR])) {
				$this->request->setUserAgent(BSUserAgent::create($name));
			}
		}

		$names = new BSArray(array(
			BSTridentUserAgent::ACCESSOR => BSTridentUserAgent::DEFAULT_NAME,
			BSWebKitUserAgent::ACCESSOR => BSWebKitUserAgent::DEFAULT_NAME,
		));
		foreach ($names as $field => $name) {
			if ($this->request[$field] || $this->user->getAttribute($field)) {
				$this->user->setAttribute($field, 1);
				$this->request->setUserAgent(BSUserAgent::create($name));
				break;
			}
		}

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
			$this->controller->registerAction($action);
		}
	}
}

/* vim:set tabstop=4: */
