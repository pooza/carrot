<?php
/**
 * @package org.carrot-framework
 * @subpackage database.table
 */

/**
 * 一時テーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTemporaryTableHandler extends BSTableHandler {
	private $name;

	/**
	 * @access public
	 */
	public function __destruct () {
		$this->getDatabase()->deleteTable($this->getName());
	}

	/**
	 * レコード追加可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isInsertable () {
		return true;
	}

	/**
	 * テーブル名を返す
	 *
	 * @access public
	 * @return string テーブル名
	 */
	public function getName () {
		if (!$this->name) {
			$name = array(
				$this->getRecordClassName(),
				BSUtility::getUniqueID(),
			);
			$this->name = strtolower(implode('_', $name));
		}
		return $this->name;
	}

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClassName () {
		return null;
	}
}

/* vim:set tabstop=4: */
