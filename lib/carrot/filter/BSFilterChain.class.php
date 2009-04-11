<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * フィルタチェーン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSFilterChain implements IteratorAggregate {
	private $chain;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->chain = new BSArray;
	}

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		foreach ($this as $filter) {
			$filter->execute();
		}
	}

	/**
	 * フィルタをチェーンに加える
	 *
	 * @access public
	 * @param BSFilter $filter フィルタ
	 */
	public function register (BSFilter $filter) {
		$this->chain[$filter->getName()] = $filter;
	}

	/**
	 * グローバルフィルタをフィルタチェーンに加える
	 *
	 * @access public
	 */
	public function loadGlobal () {
		$this->load('filters/carrot');
		$this->load('filters/application');

		$name = 'filters/' . BSController::getInstance()->getHost()->getName();
		if ($file = BSConfigManager::getConfigFile($name)) {
			$this->load($file);
		}
	}

	/**
	 * モジュールフィルタをフィルタチェーンに加える
	 *
	 * @access public
	 * @param BSModule $module モジュール
	 */
	public function loadModule (BSModule $module) {
		if ($file = $module->getConfigFile('filters')) {
			$this->load($file);
		}
	}

	/**
	 * アクションフィルタをフィルタチェーンに加える
	 *
	 * @access public
	 * @param BSAction $action アクション
	 */
	public function loadAction (BSAction $action) {
		$this->loadModule($action->getModule());
		foreach ((array)$action->getConfig('filters') as $row) {
			$row = new BSArray($row);
			if ($row['enabled']) {
				if (!$this->chain[$row['class']]) {
					$filter = BSClassLoader::getInstance()->getObject($row['class']);
					$filter->initialize((array)$row['params']);
					$this->register($filter);
				}
			} else {
				$this->chain->removeParameter($row['class']);
			}
		}
	}

	/**
	 * フィルタチェーンに加える
	 *
	 * @access private
	 * @param mixed $file 設定ファイル名、又はBSFileオブジェクト
	 */
	private function load ($file) {
		$objects = array();
		require(BSConfigManager::getInstance()->compile($file));
		if ($objects) {
			foreach ($objects as $filter) {
				$this->register($filter);
			}
		}
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return $this->chain->getIterator();
	}
}

/* vim:set tabstop=4: */
