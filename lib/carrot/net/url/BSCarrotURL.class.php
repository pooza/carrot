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
class BSCarrotURL extends BSHTTPURL {
	private $module;
	private $action;
	private $id;

	/**
	 * モジュール名を返す
	 *
	 * @access public
	 * @return string モジュール名
	 */
	public function getModuleName () {
		if (!$this->module) {
			$this->module = BS_MODULE_DEFAULT_MODULE;
		}
		return $this->module;
	}

	/**
	 * モジュール名を設定
	 *
	 * @access public
	 * @param mixed $module モジュール又はその名前
	 */
	public function setModuleName ($module) {
		if ($module instanceof BSModule) {
			$this->module = $module->getName();
		} else {
			$this->module = $module;
		}
		$this->parsePath();
	}

	/**
	 * アクション名を返す
	 *
	 * @access public
	 * @return string アクション名
	 */
	public function getActionName () {
		if (!$this->action) {
			$this->action = BS_MODULE_DEFAULT_ACTION;
		}
		return $this->action;
	}

	/**
	 * アクション名を設定
	 *
	 * @access public
	 * @param mixed $action アクション又はその名前
	 */
	public function setActionName ($action) {
		if ($action instanceof BSAction) {
			$this->module = $action->getModule()->getName();
			$this->action = $action->getName();
		} else {
			$this->action = $action;
		}
		$this->parsePath();
	}

	/**
	 * レコードのIDを返す
	 *
	 * @access public
	 * @return integer レコードのID
	 */
	public function getRecordID () {
		return $this->id;
	}

	/**
	 * レコードのIDを設定
	 *
	 * @access public
	 * @param mixed $id レコード又はそのID
	 */
	public function setRecordID ($id) {
		if ($id instanceof BSRecord) {
			$this->id = $id->getID();
		} else {
			$this->id = $id;
		}
		$this->parsePath();
	}

	/**
	 * パスをパース
	 *
	 * @access private
	 */
	private function parsePath () {
		$path = new BSArray;
		$path[] = null;
		$path[] = $this->getModuleName();
		$path[] = $this->getActionName();
		if ($id = $this->getRecordID()) {
			$path[] = $id;
		}
		$this['path'] = $path->join('/');
	}
}

/* vim:set tabstop=4: */
