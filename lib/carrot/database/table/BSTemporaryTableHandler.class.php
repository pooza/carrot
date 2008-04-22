<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * 一時テーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSTemporaryTableHandler extends BSTableHandler {
	private $name;

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	public function __destruct () {
		$this->database->deleteTable($this->getName());
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
				BSDate::getNow('YmdHis'),
				BSNumeric::getRandom(),
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

/* vim:set tabstop=4 ai: */
?>