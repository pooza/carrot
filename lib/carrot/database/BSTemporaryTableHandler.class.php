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
 * @todo 今のところMySQLだけなので、他のDBMSに対応する様に。
 */
class BSTemporaryTableHandler extends BSTableHandler {
	private $name;
	private $keyFields = array('id');

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string[] $fields フィールド定義
	 */
	public function __construct ($fields) {
		if (!is_array($fields)) {
			$fields = array($fields);
		}

		$query = sprintf(
			'CREATE TEMPORARY TABLE %s (%s) Engine=MEMORY',
			$this->getName(),
			implode(',', $fields)
		);
		$this->database->exec($query);
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	public function __destruct () {
		$this->database->exec('DROP TABLE ' . $this->getName());
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
			$this->name = sprintf(
				'temporary_%s_%s',
				BSDate::getNow('YmdHis'),
				BSNumeric::getRandom()
			);
		}
		return $this->name;
	}

	/**
	 * 主キーフィールド名を返す
	 *
	 * @access public
	 * @return string[] 主キーフィールド名
	 */
	public function getKeyFields () {
		return $this->keyFields;
	}

	/**
	 * 主キーフィールド名を設定する
	 *
	 * @access public
	 * @param string[] $fields 主キーフィールド名
	 */
	public function setKeyFields ($fields) {
		if (!is_array($fields)) {
			$fields = array($fields);
		}
		$this->keyFields = $fields;
	}

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClassName () {
		throw new BSDatabaseException(
			'"%s"のクラス名が正しくありません。', get_class($this)
		);
	}
}

/* vim:set tabstop=4 ai: */
?>