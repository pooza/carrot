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
	protected $action;

	/**
	 * @access public
	 * @param BSAction $action アクション
	 */
	public function __construct (BSAction $action) {
		$this->action = $action;
		foreach ($this->getConfigFiles() as $file) {
			if ($filters = BSConfigManager::getInstance()->compile($file)) {
				$filters = new BSArray($filters);
				foreach ($filters as $filter) {
					$filter['action'] = $action;
					$this[] = $filter;
				}
			}
		}
	}

	/**
	 * フィルタ設定ファイルの配列を返す
	 *
	 * @access protected
	 * @return BSArray 設定ファイルの配列
	 */
	protected function getConfigFiles () {
		$files = new BSArray;
		$files[] = 'filters/carrot';
		$files[] = 'filters/application';
		$files[] = 'filters/' . BSController::getInstance()->getHost()->getName();
		if ($file = $this->action->getModule()->getConfigFile('filters')) {
			$files[] = $file;
		}
		return $files;
	}

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		$this[] = new BSExecutionFilter(array(
			'action' => $this->action,
		));
		foreach ($this as $filter) {
			if ($filter->execute()) {
				exit;
			}
			$filter->setExecuted();
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
		if (($filter instanceof BSFilter) && $filter->isExecutable()) {
			if (BSString::isBlank($name)) {
				$name = $filter->getName();
			}
			parent::setParameter($name, $filter, $position);
		}
	}
}

/* vim:set tabstop=4: */
