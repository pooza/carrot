<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * フィルタセット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
class BSFilterSet extends BSArray {
	private $action;
	static private $executed;

	/**
	 * @access public
	 * @param BSAction $action アクション
	 */
	public function __construct (BSAction $action) {
		$this->action = $action;
		$this->load('filters/carrot');
		$this->load('filters/application');
		$this->load('filters/' . BSController::getInstance()->getHost()->getName());
		$this->load($action->getModule()->getConfigFile('filters'));

		// module.yamlのロード。廃止予定。
		foreach ((array)$action->getConfig('filters') as $row) {
			$row = new BSArray($row);
			if ($row['enabled']) {
				if (!$this[$row['class']]) {
					$filter = BSClassLoader::getInstance()->getObject($row['class']);
					$filter->initialize((array)$row['params']);
					$this[] = $filter;
				}
			} else {
				$this->removeParameter($row['class']);
			}
		}
	}

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		$this[] = new BSExecutionFilter;
		foreach ($this as $filter) {
			if ($filter->execute()) {
				exit;
			}
			$this->setExecuted($filter);
		}
	}

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param mixed $filter 要素（フィルタ）
	 * @param boolean $position 先頭ならTrue
	 */
	public function setParameter ($name, $filter, $position = self::POSITION_BOTTOM) {
		if (($filter instanceof BSFilter) && $this->isRegisterable($filter)) {
			if (BSString::isBlank($name)) {
				$name = $filter->getName();
			}
			parent::setParameter($name, $filter, $position);
		}
	}
	private function isRegisterable (BSFilter $filter) {
		$executed = $this->getExecuted();
		$actions = new BSArray($filter['excluded_actions']);
		return (($filter->isMultiExecutable() || !$executed[$filter->getName()])
			&& !$actions->isContain($this->action->getName())
		);
	}

	private function getExecuted () {
		if (!self::$executed) {
			self::$executed = new BSArray;
		}
		return self::$executed;
	}

	private function setExecuted (BSFilter $filter) {
		$executed = $this->getExecuted();
		$executed[$filter->getName()] = 1;
	}

	private function load ($file) {
		if ($filters = BSConfigManager::getInstance()->compile($file)) {
			foreach ((array)$filters as $filter) {
				$this[] = $filter;
			}
		}
	}
}

/* vim:set tabstop=4: */
