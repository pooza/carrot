<?php
/**
 * @package org.carrot-framework
 * @subpackage database.table
 */

/**
 * キャッシュ機能つきデータベーステーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSCachedTableHandler extends BSTableHandler {

	/**
	 * @access public
	 * @param mixed $criteria 抽出条件
	 * @param mixed $order ソート順
	 */
	public function __construct ($criteria = null, $order = null) {
		$this->getContents();
	}

	/**
	 * 出力フィールド文字列を設定
	 *
	 * @access public
	 * @param mixed $fields 配列または文字列による出力フィールド
	 */
	public function setFields ($fields) {
		if ($fields) {
			throw new BSDatabaseException('変更出来ません。');
		}
	}

	/**
	 * 名前からIDを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return integer ID
	 */
	public function getID ($name) {
		foreach ($this as $record) {
			if ($record->getAttribute('name') == $name) {
				return $record->getID();
			}
		}
	}

	/**
	 * 結果を返す
	 *
	 * @access public
	 * @return string[] 結果の配列
	 */
	public function getResult () {
		if ($result = BSController::getInstance()->getAttribute(get_class($this))) {
			$this->setExecuted(true);
			return $result;
		} else {
			return $this->query();
		}
	}

	/**
	 * クエリーを送信し直して結果を返す
	 *
	 * @access public
	 * @return string[] 結果の配列
	 */
	public function query () {
		$result = parent::query();
		BSController::getInstance()->setAttribute($this, $result);
		return $result;
	}
}

/* vim:set tabstop=4: */
