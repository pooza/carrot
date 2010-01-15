<?php
/**
 * @package org.carrot-framework
 * @subpackage database
 */

/**
 * データベース接続
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSDatabase extends PDO implements ArrayAccess, BSAssignable {
	protected $attributes;
	protected $tables;
	private $name;
	static private $instances;
	const WITHOUT_LOGGING = 1;
	const WITHOUT_SERIALIZE = 2;

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
			if (mb_ereg('^([[:alnum:]]+):', $constants['PDO_' . $name . '_DSN'], $matches)) {
				switch ($matches[1]) {
					case 'mysql':
						return self::$instances[$name] = BSMySQLDatabase::connect($name);
					case 'pgsql':
						return self::$instances[$name] = BSPostgreSQLDatabase::connect($name);
					case 'sqlite':
						return self::$instances[$name] = BSSQLiteDatabase::connect($name);
					case 'odbc':
						return self::$instances[$name] = BSODBCDatabase::connect($name);
				}
			}
			throw new BSDatabaseException('"%s"のDSNが適切ではありません。', $name);
		}
		return self::$instances[$name];
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * パスワードの候補を配列で返す
	 *
	 * @access protected
	 * @name string $name データベース名
	 * @return BSArray パスワードの候補
	 */
	static protected function getPasswords ($name) {
		$constants = BSConstantHandler::getInstance();
		$passwords = new BSArray;
		$password = $constants['PDO_' . $name . '_PASSWORD'];
		$passwords[] = $password;

		if (!BSString::isBlank($password)) {
			$passwords[] = BSCrypt::getInstance()->decrypt($password);
		}
		return $passwords;
	}

	/**
	 * テーブル名のリストを配列で返す
	 *
	 * @access public
	 * @return BSArray テーブル名のリスト
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
		return $this->getAttributes()->getParameter($name);
	}

	/**
	 * 属性を全て返す
	 *
	 * @access public
	 * @return BSArray 属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = new BSArray;
			$this->parseDSN();
		}
		return $this->attributes;
	}

	/**
	 * DSNを返す
	 *
	 * @access public
	 * @return string DSN
	 */
	public function getDSN () {
		return $this['dsn'];
	}

	/**
	 * DSNをパースしてプロパティに格納
	 *
	 * @access protected
	 */
	protected function parseDSN () {
		$constants = BSConstantHandler::getInstance();
		$this->attributes['connection_name'] = $this->getName();
		$this->attributes['dsn'] = $constants['PDO_' . $this->getName() . '_DSN'];
		$this->attributes['uid'] = $constants['PDO_' . $this->getName() . '_UID'];
		$this->attributes['password'] = $constants['PDO_' . $this->getName() . '_PASSWORD'];
		$this->attributes['dbms'] = $this->getDBMS();
		$this->attributes['version'] = $this->getVersion();
		$this->attributes['encoding'] = $this->getEncoding();
	}

	/**
	 * バージョンを返す
	 *
	 * @access protected
	 * @return float バージョン
	 */
	protected function getVersion () {
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
		if (BS_PDO_QUERY_LOG_ENABLE) {
			$this->putLog($query);
		}
		return $r;
	}

	/**
	 * クエリーをエンコード
	 *
	 * @access protected
	 * @param string $query クエリー文字列
	 * @return string エンコードされたクエリー
	 */
	protected function encodeQuery ($query) {
		return BSString::convertEncoding($query, $this['encoding'], 'utf-8');
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
		$class = BSClassLoader::getInstance()->getClass($this['dbms'], 'TableProfile');
		return new $class($table, $this);
	}

	/**
	 * 抽出条件オブジェクトを生成して返す
	 *
	 * @access public
	 * @return BSCriteriaSet 抽出条件
	 */
	public function createCriteriaSet () {
		$criteria = new BSCriteriaSet;
		$criteria->setDatabase($this);
		return $criteria;
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
	 * データベースのインスタンス名を設定
	 *
	 * @access public
	 * @return string インスタンス名
	 */
	public function setName ($name) {
		$this->name = $name;
	}

	/**
	 * 文字列をクォート
	 *
	 * @access public
	 * @param mixed $value 対象の文字列または配列
	 * @param string $type クォートのタイプ
	 * @return string クォート後の文字列
	 */
	public function quote ($value, $type = self::PARAM_STR) {
		if (BSArray::isArray($value)) {
			$values = $value;
			foreach ($values as $key => $value) {
				$values[$key] = self::quote($value, $type);
			}
			return $values;
		} else if (BSString::isBlank($value)) {
			return 'NULL';
		} else {
			return parent::quote($value, $type);
		}
	}

	/**
	 * ログを書き込む
	 *
	 * @access public
	 * @param mixed $log ログメッセージの文字列、又はBSStringFormat
	 */
	public function putLog ($log) {
		if ($this->isLoggable()) {
			BSLogManager::getInstance()->put($log, $this);
		}
	}

	/**
	 * クエリーログを使用するか？
	 *
	 * @access private
	 * @return boolean クエリーログを使用するならTrue
	 */
	private function isLoggable () {
		return BSController::getInstance()->getAttribute('PDO_' . $this->getName() . '_LOGGABLE');
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
	 * テーブルを削除
	 *
	 * @access public
	 * @param string $table テーブル名
	 */
	public function deleteTable ($table) {
		$this->exec(BSSQL::getDropTableQueryString($table));
	}

	/**
	 * テーブルを削除
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
	 * ダンプファイルを生成
	 *
	 * @access public
	 * @param string $suffix ファイル名サフィックス
	 * @param BSDirectory $dir 出力先ディレクトリ
	 * @return BSFile ダンプファイル
	 */
	public function createDumpFile ($suffix = null, BSDirectory $dir = null) {
		return null;
	}

	/**
	 * スキーマファイルを生成
	 *
	 * @access public
	 * @param string $suffix ファイル名サフィックス
	 * @param BSDirectory $dir 出力先ディレクトリ
	 * @return BSFile スキーマファイル
	 */
	public function createSchemaFile ($suffix = null, BSDirectory $dir = null) {
		return null;
	}

	/**
	 * 最適化
	 *
	 * @access public
	 */
	public function optimize () {
	}

	/**
	 * 最適化
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
	 * @access private
	 * @return string DBMS
	 */
	private function getDBMS () {
		if (!mb_ereg('^BS([[:alpha:]]+)Database$', get_class($this), $matches)) {
			throw new BSDatabaseException(get_class($this) . 'のDBMS名が正しくありません。');
		}
		return $matches[1];
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード
	 */
	public function getEncoding () {
		return 'utf-8';
	}

	/**
	 * 要素が存在するか？
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->attributes->hasParameter($key);
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BSDatabaseException('データベースの属性を直接更新することはできません。');
	}

	/**
	 * 要素を削除
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BSDatabaseException('データベースの属性は削除できません。');
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		$values = array(
			'name' => $this->getName(),
			'tables' => $this->getTableNames()->getParameters(),
		);
		foreach ($this->getAttributes() as $key => $value) {
			if (in_array($key, array('uid', 'password', 'user'))) {
				continue;
			} else if ($value instanceof BSFile) {
				$values['attributes'][$key] = $value->getPath();
			} else if ($value instanceof BSHost) {
				$values['attributes'][$key] = $value->getName();
			} else {
				$values['attributes'][$key] = $value;
			}
		}
		return $values;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('データベース "%s"', $this->getName());
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
			$pattern = '^' . BSConstantHandler::PREFIX . '_PDO_([[:upper:]]+)_DSN$';
			if (mb_ereg($pattern, $key, $matches)) {
				$name = BSString::toLower($matches[1]);
				try {
					$databases[$name] = self::getInstance($name)->getAttributes();
				} catch (BSDatabaseException $e) {
				}
			}
		}
		return $databases;
	}
}

/* vim:set tabstop=4: */
