<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * 偽装されたUserAgent
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFakeUserAgentFilter extends BSFilter {
	public function execute () {
		if (BS_DEBUG || $this->user->isAdministrator()) {
			if (!BSString::isBlank($name = $this->request[BSUserAgent::ACCESSOR])) {
				$this->request->setUserAgent(BSUserAgent::create($name));
				return;
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
	}
}

/* vim:set tabstop=4: */
