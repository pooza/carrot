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

	/**
	 * 禁止されたUserAgentのタイプ
	 *
	 * @access private
	 * @return 許可されたらTrue
	 */
	private function getDeniedTypes () {
		if ($this['types'] instanceof BSArray) {
			//素通り
		} else if (is_array($this['types'])) {
			$this['types'] = new BSArray($this['types']);
		} else {
			$this['types'] = BSString::explode(',', $this['types']);
		}
		return $this['types'];
	}

	/**
	 * 許可されたUserAgentか？
	 *
	 * @access private
	 * @param BSUserAgent $useragent 対象
	 * @return 許可されたらTrue
	 */
	private function isAllowed (BSUserAgent $useragent) {
		return !$this->getDeniedTypes()->isIncluded($useragent->getType());
	}

	public function initialize ($parameters = array()) {
		$this->setParameter('types', 'LegacyMozilla');
		$this->setParameter('module', 'Default');
		$this->setParameter('action', 'DeniedUserAgent');
		return parent::initialize($parameters);
	}

	public function execute (BSFilterChain $filters) {
		if (!$this->isAllowed($this->request->getUserAgent())) {
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

/* vim:set tabstop=4 ai: */
?>