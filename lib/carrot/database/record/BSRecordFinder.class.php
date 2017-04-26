<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.record
 */

/**
 * レコード検索
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSRecordFinder extends BSParameterHolder {
	use BSBasicObject;
	private $table;
	private $record;

	/**
	 * @access public
	 * @param BSParameterHolder $params 要素の配列
	 */
	public function __construct (BSParameterHolder $params) {
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
				$this->record = $this->controller->getModule()->getRecord();
			}
		}
		return $this->record;
	}

	private function getTable () {
		if (!$this->table) {
			try {
				if (BSString::isBlank($class = $this['class'])) {
					$this->table = $this->controller->getModule()->getTable();
				} else {
					$this->table = BSTableHandler::create($class);
				}
			} catch (Exception $e) {
			}
		}
		return $this->table;
	}
}

/* vim:set tabstop=4: */
