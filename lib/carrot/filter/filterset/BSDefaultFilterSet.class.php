<?php
/**
 * @package org.carrot-framework
 * @subpackage filter.filterset
 */

/**
 * 規定フィルタセット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
class BSDefaultFilterSet extends BSArray {

	/**
	 * @access public
	 */
	public function __construct () {
		foreach ($this->getConfigFiles() as $file) {
			if ($filters = BSConfigManager::getInstance()->compile($file)) {
				foreach ($filters as $filter) {
					$this[] = $filter;
				}
			}
		}
		$this[] = new BSExecutionFilter;
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

		$module = BSController::getInstance()->getModule();
		if ($file = $module->getConfigFile('filters')) {
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
		foreach ($this as $filter) {
			if ($filter->isExecutable()) {
				if ($filter->execute() == BSController::COMPLETED) {
					exit;
				}
				$filter->setExecuted();
			}
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
		if (($filter instanceof BSFilter) == false) {
			throw new BSFilterException('フィルターセットに加えられません。');
		}
		if (BSString::isBlank($name)) {
			$name = $filter->getName();
		}
		parent::setParameter($name, $filter, $position);
	}
}

/* vim:set tabstop=4: */
