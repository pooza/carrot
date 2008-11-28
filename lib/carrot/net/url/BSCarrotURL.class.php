<?php
/**
 * @package org.carrot-framework
 * @subpackage net.url
 */

/**
 * CarrotアプリケーションのURL
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCarrotURL extends BSURL {
	private $module;
	private $action;
	private $id;

	public function getModuleName () {
		if (!$this->module) {
			$this->module = BSController::getInstance()->getConstant('DEFAULT_MODULE');
		}
		return $this->module;
	}

	public function setModuleName ($module) {
		if ($module instanceof BSModule) {
			$this->module = $module->getName();
		} else {
			$this->module = $module;
		}
		$this->parsePath();
	}

	public function getActionName () {
		if (!$this->action) {
			$this->action = BSController::getInstance()->getConstant('DEFAULT_ACTION');
		}
		return $this->action;
	}

	public function setActionName ($action) {
		if ($action instanceof BSAction) {
			$this->module = $action->getModule()->getName();
			$this->action = $action->getName();
		} else {
			$this->action = $action;
		}
		$this->parsePath();
	}

	public function getRecordID () {
		return $this->id;
	}

	public function setRecordID ($id) {
		if ($module instanceof BSRecord) {
			$this->id = $id->getID();
		} else {
			$this->id = $id;
		}
		$this->parsePath();
	}

	private function parsePath () {
		$path = new BSArray;
		$path[] = null;
		$path[] = $this->getModuleName();
		$path[] = $this->getActionName();
		if ($id = $this->getRecordID()) {
			$path[] = $id;
		}
		$this->setAttribute('path', $path->join('/'));
	}
}

/* vim:set tabstop=4 ai: */
?>