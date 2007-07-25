<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * PDOのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSDatabase extends PDO {
	protected $host;
	protected $port;
	protected $name;
	protected $dsn;
	protected $user;
	protected $tables = array();
	private $dbms;
	const DSN = BS_PDO_DSN;
	const UID = BS_PDO_UID;
	const PASSWORD = BS_PDO_PASSWORD;
	const LOG_TYPE = 'Query';

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSDatabase インスタンス
	 * @static
	 */
	public static function getInstance () {
		preg_match('/^([a-z0-9]+):/', self::DSN, $matches);
		switch ($dbms = $matches[1]) {
			case 'mysql':
				return BSMySQL::getInstance();
			case 'pgsql':
				return BSPostgreSQL::getInstance();
			case 'sqlite':
			case 'sqlite2':
				return BSSQLite::getInstance();
			case 'odbc':
				return BSODBCDatabase::getInstance();
			default:
				throw new BSDatabaseException('DBMS"%s"が適切ではありません。', $dbms);
		}
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
	 * テーブル名のリストを配列で返す
	 *
	 * @access public
	 * @return string[] テーブル名のリスト
	 * @abstract
	 */
	abstract public function getTableNames ();

	/**
	 * クエリーを実行してPDOStatementを返す
	 *
	 * @access public
	 * @return PDOStatement
	 * @param string $query クエリー文字列
	 */
	public function query ($query) {
		$query = BSString::convertEncoding($query);
		if (!$rs = parent::query($query)) {
			throw new BSDatabaseException(
				'実行不能なクエリーです。(%s) [%s]',
				$this->getError(),
				$query
			);
		}
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		return $rs;
	}

	/**
	 * クエリーを実行
	 *
	 * @access public
	 * @return integer 影響した行数
	 * @param string $query クエリー文字列
	 */
	public function exec ($query) {
		$query = BSString::convertEncoding($query);
		$r = parent::exec($query);
		if ($r === false) {
			throw new BSDatabaseException(
				'実行不能なクエリーです。(%s) [%s]',
				$this->getError(),
				$query
			);
		}
		if (BSController::getInstance()->isDebugMode()) {
			$this->putQueryLog($query);
		}
		return $r;
	}

	/**
	 * 直近のエラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		$err = self::errorInfo();
		return BSString::convertEncoding($err[2]);
	}

	/**
	 * テーブルのプロフィールを返す
	 *
	 * @access public
	 * @param string $table テーブルの名前
	 * @return BSTableProfile テーブルのプロフィール
	 */
	public function getTableProfile ($table) {
		$class = 'BS' . $this->getDBMS() . 'TableProfile';
		return new $class($table);
	}

	/**
	 * データベース名を返す
	 *
	 * @access public
	 * @return string データベース名
	 */
	public function getDatabaseName () {
		return $this->name;
	}

	/**
	 * データベース名を返す - getDatabaseNameのエイリアス
	 *
	 * @access public
	 * @return string データベース名
	 */
	public function getName () {
		return $this->getDatabaseName();
	}

	/**
	 * 文字列をクォート
	 *
	 * @access public
	 * @param string $str クォートの対象
	 * @param boolean $convert 半角/全角標準化を行うか
	 * @return string クォートされた文字列
	 */
	public function quote ($str, $convert = true) {
		if ($str != '') {
			$str = BSString::convertEncoding($str);
			if ($convert) {
				$str = BSString::convertKana($str);
			}
			$str = str_replace("\r\n", "\n", $str);
			$str = str_replace("\r", "\n", $str);

			return parent::quote($str);
		} else {
			return 'NULL';
		}
	}

	/**
	 * クエリーログを書き込む
	 *
	 * @access protected
	 * @param string $query クエリーログ
	 */
	protected function putQueryLog ($query) {
		BSLog::put($query, self::LOG_TYPE);
	}

	/**
	 * DSNスキーマを返す
	 *
	 * @access public
	 * @return string DSNスキーマ
	 */
	public function getScheme () {
		return strtolower($this->getDBMS());
	}

	/**
	 * DSNを返す
	 *
	 * @access public
	 * @return string DSN
	 */
	public function getDSN () {
		return $this->dsn;
	}

	/**
	 * DBMSを返す
	 *
	 * @access public
	 * @return string DBMS
	 */
	public function getDBMS () {
		if (!$this->dbms) {
			if (preg_match('/^BS([A-Za-z]+)$/', get_class($this), $matches)) {
				$this->dbms = $matches[1];
			} else {
				throw new BSDatabaseException(
					get_class($this) . 'のDBMS名が正しくありません。'
				);
			}
		}
		return $this->dbms;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('データベース "%s"', $this->getDSN());
	}
}

/* vim:set tabstop=4 ai: */
?>