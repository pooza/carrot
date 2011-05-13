<?php
/**
 * @package org.carrot-framework
 * @subpackage database.table
 */

/**
 * シリアライズ可能なデータベーステーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSSerializableTableHandler extends BSTableHandler implements BSSerializable {

	/**
	 * @access public
	 * @param mixed $criteria 抽出条件
	 * @param mixed $order ソート順
	 */
	public function __construct ($criteria = null, $order = null) {
		if (!$this->getSerialized()) {
			$this->serialize();
		}
		$this->setExecuted(true);
	}

	/**
	 * 出力フィールド文字列を設定
	 *
	 * @access public
	 * @param mixed $fields 配列または文字列による出力フィールド
	 */
	public function setFields ($fields) {
		if ($fields) {
			throw new BSDatabaseException('変更できません。');
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
		return $this->getSerialized();
	}

	/**
	 * シリアライズのダイジェストを返す
	 *
	 * @access public
	 * @return string 属性名
	 */
	public function digestSerialized () {
		return get_class($this);
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		BSController::getInstance()->setAttribute($this, parent::getResult());
	}

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		return BSController::getInstance()->getAttribute($this);
	}
}

/* vim:set tabstop=4: */
