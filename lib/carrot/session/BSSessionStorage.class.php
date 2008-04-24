<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage session
 */

/**
 * セッションストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSessionStorage extends ParameterHolder {
	const SESSION_NAME = 'Carrot';
	const TABLE_NAME = 'stored_session';
	private $table;
	private static $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化禁止
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSSessionStorage インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSSessionStorage();
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ
	 */
	public function initialize ($parameters = null) {
		if (!$this->getParameter('session_name')) {
			$this->setParameter('session_name', self::SESSION_NAME);
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

	/**
	 * セッション変数を返す
	 *
	 * @access public
	 * @param string $key 変数名
	 * @return mixed セッション変数
	 */
	public function read ($key) {
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
	}

	/**
	 * セッション変数を書き込む
	 *
	 * @access public
	 * @param string $key 変数名
	 * @param mixed $value 値
	 */
	public function write ($key, $value) {
		$_SESSION[$key] = $value;
	}

	/**
	 * セッション変数を削除する
	 *
	 * @access public
	 * @param string $key 変数名
	 */
	public function remove ($key) {
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * シャットダウン
	 *
	 * 実際には何もしない
	 *
	 * @access public
	 */
	public function shutdown () {
	}
}
?>