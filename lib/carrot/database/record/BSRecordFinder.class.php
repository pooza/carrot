<?php
/**
 * @package org.carrot-framework
 * @subpackage database.record
 */

/**
 * レコード検索
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSRecordFinder extends BSParameterHolder {
	private $table;
	private $record;

	/**
	 * @access public
	 * @param mixed[] $params 要素の配列
	 */
	public function __construct ($params = array()) {
		$this->setParameters($params);
	}

	/**
	 * 検索実行
	 *
	 * @access public
	 * @param integer $id ID
	 * @return BSRecord レコード
	 */
	public function execute ($id = null) {
		if (($record = $this->getRecord($id)) && ($record instanceof BSRecord)) {
			return $record;
		}
	}

	private function getRecord ($id) {
		if (!$this->record) {
			if (!$id) {
				$id = $this['id'];
			}
			if (($table = $this->getTable()) && ($record = $table->getRecord($id))) {
				$this->record = $record;
			} else if (BSString::isBlank($this['class'])) {
				$module = BSController::getInstance()->getModule();
				$this->record = $module->getRecord();
			}
		}
		return $this->record;
	}

	private function getTable () {
		if (!$this->table) {
			try {
				if (BSString::isBlank($this['class'])) {
					$module = BSController::getInstance()->getModule();
					$this->table = $module->getTable();
				} else {
					$this->table = BSTableHandler::create($this['class']);
				}
			} catch (Exception $e) {
			}
		}
		return $this->table;
	}
}

/* vim:set tabstop=4: */
