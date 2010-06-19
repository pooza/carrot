<?php
/**
 * @package org.carrot-framework
 * @subpackage database.record
 */

/**
 * レコード検索
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRecordFinder extends BSParameterHolder {
	private $table;

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
		if (!$id) {
			$id = $this['id'];
		}
		if (($table = $this->getTable())
			&& ($record = $table->getRecord($id))
			&& ($record instanceof BSRecord)) {

			return $record;
		}
	}

	private function getTable () {
		if (!$this->table) {
			try {
				if (BSString::isBlank($this['class'])) {
					$this->table = BSController::getInstance()->getModule()->getTable();
				} else {
					$this->table = BSTableHandler::getInstance($this['class']);
				}
			} catch (Exception $e) {
			}
		}
		return $this->table;
	}
}

/* vim:set tabstop=4: */
