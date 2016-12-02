<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage serialize.storage.database
 */

/**
 * シリアライズテーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSerializeEntryHandler extends BSTableHandler {

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
	 * レコード作成
	 *
	 * @access public
	 * @param mixed $values 値
	 * @param integer $flags フラグのビット列
	 *   BSDatabase::WITHOUT_LOGGING ログを残さない
	 * @return string レコードの主キー
	 */
	public function createRecord ($values, $flags = BSDatabase::WITHOUT_LOGGING) {
		$db = $this->getDatabase();
		$query = BSSQL::getInsertQueryString($this->getName(), $values, $db);
		$db->exec($query);
		return $values[$this->getKeyField()];
	}

	/**
	 * レコードの全消去が可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isClearable () {
		return true;
	}

	/**
	 * テーブル名を返す
	 *
	 * @access public
	 * @return string テーブル名
	 */
	public function getName () {
		return BSDatabaseSerializeStorage::TABLE_NAME;
	}

	/**
	 * データベースを返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		return BSDatabase::getInstance('serialize');
	}

	/**
	 * スキーマを返す
	 *
	 * @access public
	 * @return BSArray フィールド情報の配列
	 */
	public function getSchema () {
		return new BSArray([
			'id' => 'varchar(128) NOT NULL PRIMARY KEY',
			'update_date' => 'timestamp NOT NULL',
			'data' => 'TEXT',
		]);
	}
}

/* vim:set tabstop=4: */
