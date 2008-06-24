<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * データベース接続
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSDatabase extends PDO {
	protected $attributes;
	protected $tables = array();
	private $dbms;
	private $name;
	static private $instances;
	const LOG_TYPE = 'Database';

	/**
	 * フライウェイトインスタンスを返す
	 *
	 * @access public
	 * @name string $name データベース名
	 * @return BSDatabase インスタンス
	 * @static
	 */
	static public function getInstance ($name = 'default') {
		if (!self::$instances) {
			self::$instances = new BSArray;
		}
		if (!self::$instances[$name]) {
			$constants = BSConstantHandler::getInstance();
			if (!preg_match('/^([a-z0-9]+):/', $constants['PDO_' . $name . '_DSN'], $matches)) {
				throw new BSDatabaseException('"%s" のDSNが適切ではありません。', $name);
			}
			switch ($dbms = $matches[1]) {
				case 'mysql':
					return self::$instances[$name] = BSMySQLDatabase::getInstance($name);
				case 'pgsql':
					return self::$instances[$name] = BSPostgreSQLDatabase::getInstance($name);
				case 'sqlite':
					return self::$instances[$name] = BSSQLiteDatabase::getInstance($name);
				case 'odbc':
					return self::$instances[$name] = BSODBCDatabase::getInstance($name);
				default:
					throw new BSDatabaseException('"%s" のDBMSが適切ではありません。', $name);
			}
		}
		return self::$instances[$name];
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
	 * 属性値を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性値
	 */
	public function getAttribute ($name) {
		if (!isset($this->attributes[$name])) {
			$this->parseDSN();
		}
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
	}

	/**
	 * 各種情報を返す
	 *
	 * @access public
	 * @return BSArray 各種情報
	 */
	public function getInfo () {
		$info = new BSArray;
		$info['name'] = $this->getName();
		$info['dsn'] = $this->getDSN();
		$info['dbms'] = $this->getDBMS();
		$info['version'] = $this->getVersion();
		$info['encoding'] = $this->getEncoding();
		return $info;
	}

	/**
	 * DSNを返す
	 *
	 * @access public
	 * @return string DSN
	 */
	public function getDSN () {
		return $this->getAttribute('dsn');
	}

	/**
	 * DSNをパースしてプロパティに格納する
	 *
	 * @access protected
	 */
	protected function parseDSN () {
		foreach (array('dsn', 'uid', 'password') as $key) {
			$this->attributes[$key] = BSController::getInstance()->getConstant(
				'PDO_' . $this->getName() . '_' . $key
			);
		}
	}

	/**
	 * バージョンを返す
	 *
	 * @access public
	 * @return float バージョン
	 */
	public function getVersion () {
	}

	/**
	 * クエリーを実行してPDOStatementを返す
	 *
	 * @access public
	 * @return PDOStatement
	 * @param string $query クエリー文字列
	 */
	public function query ($query) {
		if (!$rs = parent::query($this->encodeQuery($query))) {
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
		$r = parent::exec($this->encodeQuery($query));
		if ($r === false) {
			throw new BSDatabaseException(
				'実行不能なクエリーです。(%s) [%s]',
				$this->getError(),
				$query
			);
		}
		if (BSController::getInstance()->getConstant('PDO_QUERY_LOG_ENABLE')) {
			$this->putLog($query);
		}
		return $r;
	}

	/**
	 * クエリーをエンコードする
	 *
	 * @access protected
	 * @param string $query クエリー文字列
	 * @return string エンコードされたクエリー
	 */
	protected function encodeQuery ($query) {
		return BSString::convertEncoding(
			$query,
			$this->getEncoding(),
			BSString::SCRIPT_ENCODING
		);
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
		return new $class($table, $this);
	}

	/**
	 * データベースのインスタンス名を返す
	 *
	 * DSNにおける「データベース名」のことではなく、
	 * BSDatabaseクラスのフライウェイトインスタンスとしての名前のこと。
	 *
	 * @access public
	 * @return string インスタンス名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * データベースのインスタンス名を設定する
	 *
	 * @access public
	 * @return string インスタンス名
	 */
	public function setName ($name) {
		$this->name = $name;
	}

	/**
	 * 文字列をクォートする
	 *
	 * @access public
	 * @param string $string 対象文字列
	 * @param string $type クォートのタイプ
	 * @return string クォート後の文字列
	 */
	public function quote ($string, $type = PDO::PARAM_STR) {
		if ($string != '') {
			return parent::quote($string, $type);
		} else {
			return 'NULL';
		}
	}

	/**
	 * クエリーログを書き込む
	 *
	 * @access protected
	 * @param string $log ログ
	 */
	protected function putLog ($log) {
		$constants = BSConstantHandler::getInstance();
		$name = sprintf('pdo_%s_loggable', $this->getName());
		if (!$constants->hasParameter($name) || $constants[$name]) {
			BSLog::put($log, self::LOG_TYPE);
		}
	}

	/**
	 * 一時テーブルを生成して返す
	 *
	 * @access public
	 * @param string[] $details フィールド定義等
	 * @param string $class クラス名
	 * @return BSTemporaryTableHandler 一時テーブル
	 */
	public function getTemporaryTable ($details, $class = 'BSTemporaryTableHandler') {
		$table = new $class;
		$this->exec(BSSQL::getCreateTableQueryString($table->getName(), $details));
		return $table;
	}

	/**
	 * 命名規則に従い、シーケンス名を返す
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @param string $field 主キーフィールド名
	 * @return string シーケンス名
	 */
	public function getSequenceName ($table, $field = 'id') {
		return null;
	}

	/**
	 * テーブルを削除する
	 *
	 * @access public
	 * @param string $table テーブル名
	 */
	public function deleteTable ($table) {
		$this->exec(BSSQL::getDropTableQueryString($table));
	}

	/**
	 * テーブルを削除する
	 *
	 * deleteTableのエイリアス
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @final
	 */
	final public function dropTable ($table) {
		$this->deleteTable($table);
	}

	/**
	 * ダンプファイルを生成する
	 *
	 * @access public
	 * @param string $filename ファイル名
	 * @param BSDirectory $dir 出力先ディレクトリ
	 * @return BSFile ダンプファイル
	 */
	public function createDumpFile ($filename = null, BSDirectory $dir = null) {
		return null;
	}

	/**
	 * スキーマファイルを生成する
	 *
	 * @access public
	 * @param string $filename ファイル名
	 * @param BSDirectory $dir 出力先ディレクトリ
	 * @return BSFile スキーマファイル
	 */
	public function createSchemaFile ($filename = null, BSDirectory $dir = null) {
		return null;
	}

	/**
	 * 最適化する
	 *
	 * @access public
	 */
	public function optimize () {
	}

	/**
	 * 最適化する
	 *
	 * optimizeのエイリアス
	 *
	 * @access public
	 * @final
	 */
	final public function vacuum () {
		return $this->optimize();
	}

	/**
	 * DBMSを返す
	 *
	 * @access public
	 * @return string DBMS
	 */
	public function getDBMS () {
		if (!$this->dbms) {
			if (preg_match('/^BS([A-Za-z]+)Database$/', get_class($this), $matches)) {
				$this->dbms = $matches[1];
			} else {
				throw new BSDatabaseException('%sのDBMS名が正しくありません。', get_class($this));
			}
		}
		return $this->dbms;
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード
	 */
	public function getEncoding () {
		return BSString::SCRIPT_ENCODING;
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

	/**
	 * データベース情報のリストを返す
	 *
	 * @access public
	 * @return BSArray データベース情報
	 * @static
	 */
	static public function getDatabases () {
		$databases = new BSArray;
		foreach (BSConstantHandler::getInstance()->getParameters() as $key => $value) {
			if (preg_match('/_PDO_([A-Z]+)_DSN$/', $key, $matches)) {
				$name = strtolower($matches[1]);
				try {
					$databases[$name] = self::getInstance($name)->getInfo();
				} catch (BSDatabaseException $e) {
				}
			}
		}
		return $databases;
	}
}

/* vim:set tabstop=4 ai: */
?>