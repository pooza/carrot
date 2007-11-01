<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage session
 */

/**
 * SessionStorageのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSessionStorage extends SessionStorage {
	const TABLE_NAME = 'stored_session';
	private $table;

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 * @param string[] $parameters パラメータ
	 */
	public function initialize ($context, $parameters = null) {
		if (!$this->getParameter('session_name')) {
			$this->setParameter('session_name', 'Carrot');
		}

		switch ($this->getStorageType()) {
			case 'database':
				if (!BSController::getInstance()->isCLI()) {
					session_set_save_handler(
						array($this->getTable(), 'open'),
						array($this->getTable(), 'close'),
						array($this->getTable(), 'getAttribute'),
						array($this->getTable(), 'setAttribute'),
						array($this->getTable(), 'removeAttribute'),
						array($this->getTable(), 'clean')
					);
				}
				break;
		}

		if (headers_sent()) {
			throw new BSHTTPException('セッションの開始に失敗しました。');
		}
		session_start();
	}

	/**
	 * ストレージの種類を返す
	 *
	 * @access public
	 * @return string ストレージの種類
	 */
	private function getStorageType () {
		if (defined('BS_SESSION_STORAGE_TYPE')) {
			return BS_SESSION_STORAGE_TYPE;
		}
	}

	/**
	 * ストレージテーブルを返す
	 *
	 * テーブルが存在しなければ、作成しようとする。
	 *
	 * @access public
	 * @return BSTableHandler ストレージテーブル
	 */
	public function getTable () {
		if (!$this->table) {
			$db = BSDatabase::getInstance();
			if (!in_array(self::TABLE_NAME, $db->getTableNames())) {
				$fields = array(
					'id varchar(128) NOT NULL PRIMARY KEY',
					'update_date timestamp NOT NULL',
					'data TEXT',
				);
				$query = BSSQL::getCreateTableQueryString(self::TABLE_NAME, $fields);
				$db->exec($query);
			}
			$class = 'BS' . BSString::pascalize(self::TABLE_NAME) . 'Handler';
			$this->table = new $class;
		}
		return $this->table;
	}
}
?>