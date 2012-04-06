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
		if (!self::$executed) {
			self::$executed = new BSArray;
		}
		$this->action = $action;

		$files = new BSArray;
		$files[] = 'filters/carrot';
		$files[] = 'filters/application';
		$files[] = 'filters/' . BSController::getInstance()->getHost()->getName();
		$files[] = $action->getModule()->getConfigFile('filters');
		foreach ($files as $file) {
			if ($filters = BSConfigManager::getInstance()->compile($file)) {
				foreach ((array)$filters as $filter) {
					$this[] = $filter;
				}
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
		return (($filter->isRepeatable() || !$this->isExecuted($filter))
			&& !$filter->isExcludedAction($this->action)
		);
	}

	private function setExecuted (BSFilter $filter) {
		self::$executed[$filter->getName()] = 1;
	}

	private function isExecuted (BSFilter $filter) {
		return !!self::$executed[$filter->getName()];
	}
}

/* vim:set tabstop=4: */
