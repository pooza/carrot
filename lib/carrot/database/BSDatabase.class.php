<?php
/**
 * @package org.carrot-framework
 * @subpackage database
 */

/**
 * データベース接続
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSDatabase extends PDO implements ArrayAccess, BSAssignable {
	protected $tables;
	protected $dsn;
	protected $version;
	protected $profiles;
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
			$dsn = BSConstantHandler::getInstance()->getParameter('PDO_' . $name . '_DSN');
			if (mb_ereg('^([[:alnum:]]+):', $dsn, $matches)) {
				$class = BSClassLoader::getInstance()->getClass($matches[1], 'DataSourceName');
				if (($dsn = new $class($dsn, $name)) && ($db = $dsn->connect())) {
					if ($db->isLegacy()) {
						throw new BSDatabaseException($db . 'は旧式です。');
					}
					return self::$instances[$name] = $db;
				}
			}
			$message = new BSStringFormat('"%s"のDSNが適切ではありません。');
			$message[] = $name;
			throw new BSDatabaseException($message);
		}
		return self::$instances[$name];
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
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
		return $this->dsn[$name];
	}

	/**
	 * 属性を全て返す
	 *
	 * @access public
	 * @return BSArray 属性
	 */
	public function getAttributes () {
		return $this->dsn;
	}

	/**
	 * DSNを設定
	 *
	 * @access public
	 * @param BSDataSourceName $dsn DSN
	 */
	public function setDSN (BSDataSourceName $dsn) {
		$this->dsn = $dsn;
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
		if (!$rs = parent::query($query)) {
			$message = new BSStringFormat('実行不能なクエリーです。(%s) [%s]');
			$message[] = $this->getError();
			$message[] = $query;
			throw new BSDatabaseException($message);
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
		if (($r = parent::exec($query)) === false) {
			$message = new BSStringFormat('実行不能なクエリーです。(%s) [%s]');
			$message[] = $this->getError();
			$message[] = $query;
			throw new BSDatabaseException($message);
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
	 * @param string $name テーブルの名前
	 * @return BSTableProfile テーブルのプロフィール
	 */
	public function getTableProfile ($name) {
		if (!$this->profiles) {
			$this->profiles = new BSArray;
		}
		if (!$this->profiles[$name]) {
			if (!mb_ereg('^(BS)?(.+)Database$', get_class($this), $matches)) {
				throw new BSDatabaseException($this . 'のクラス名が正しくありません。');
			}
			$class = BSClassLoader::getInstance()->getClass($matches[2], 'TableProfile');
			$this->profiles[$name] = new $class($name, $this);
		}
		return $this->profiles[$name];
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
	 * @access public
	 * @return string インスタンス名
	 */
	public function getName () {
		return $this->dsn->getName();
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
		if (is_array($value) || ($value instanceof BSParameterHolder)) {
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
	public function log ($log) {
		if ($this->isLoggable()) {
			BSLogManager::getInstance()->put($log, $this);
		}
	}

	/**
	 * クエリーログを使用するか？
	 *
	 * @access protected
	 * @return boolean クエリーログを使用するならTrue
	 */
	protected function isLoggable () {
		return !!$this->getAttribute('loggable');
	}

	/**
	 * 旧式か
	 *
	 * @access public
	 * @return boolean 旧式ならTrue
	 */
	public function isLegacy () {
		return false;
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
	 * テーブルを作成
	 *
	 * @access public
	 * @param string $table テーブル名
	 * @param BSArray $schema スキーマ
	 */
	public function createTable ($table, BSArray $schema) {
		$this->exec(BSSQL::getCreateTableQueryString($table, $schema));
		$this->tables = null;
	}

	/**
	 * テーブルを削除
	 *
	 * @access public
	 * @param string $name テーブル名
	 */
	public function deleteTable ($table) {
		$this->exec(BSSQL::getDropTableQueryString($table));
		$this->tables = null;
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
	 * ダンプファイル生成してを返す
	 *
	 * @access public
	 * @param BSDirectory $dir 出力先ディレクトリ
	 * @return BSFile ダンプファイル
	 */
	public function createDumpFile (BSDirectory $dir = null) {
		if (!$dir) {
			$dir = BSFileUtility::getDirectory('dump');
		}

		try {
			$name = sprintf('%s_%s.sql', $this->getName(), BSDate::getNow('Y-m-d'));
			$file = $dir->createEntry($name);
			$file->setContents($this->dump());
			$dir->purge();
		} catch (Exception $e) {
			return;
		}

		$this->log($this . 'のダンプファイルを保存しました。');
		return $file;
	}

	/**
	 * バックアップ対象ファイルを返す
	 *
	 * @access public
	 * @return BSFile バックアップ対象ファイル
	 */
	public function getBackupTarget () {
		return $this->createDumpFile();
	}

	/**
	 * ダンプ実行
	 *
	 * @access protected
	 * @return string 結果
	 */
	protected function dump () {
		throw new BSDatabaseException($this . 'はダンプできません。');
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
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->dsn->hasParameter($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BSDatabaseException('データベースの属性を直接更新することはできません。');
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BSDatabaseException('データベースの属性は削除できません。');
	}

	/**
	 * 外部キーが有効か？
	 *
	 * @access public
	 * @return boolean 有効ならTrue
	 */
	public function hasForeignKey () {
		return true;
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
	 * データベース関数を返す
	 *
	 * @access public
	 * @param string $name 関数名
	 * @param string $value 値
	 * @param boolean $quotes クォートする
	 * @return string 関数の記述
	 */
	public function getFunction ($name, $value, $quotes = false) {
		$func = new BSStringFormat('%s(%s)');
		$func[] = $name;
		if (!!$quotes) {
			$func[] = $this->quote($value);
		} else {
			$func[] = $value;
		}
		return $func->getContents();
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
