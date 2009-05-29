<?php
/**
 * @package org.carrot-framework
 * @subpackage database.table
 */

/**
 * データベーステーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSTableHandler implements IteratorAggregate, BSDictionary, BSAssignable {
	private $fields = '*';
	private $key = 'id';
	private $criteria;
	private $order;
	private $page;
	private $pagesize = 20;
	private $lastpage;
	private $executed = false;
	private $result = array();
	private $queryString;
	private $recordClassName;
	private $name;
	private $fieldNames = array();
	private $ids;
	const CLASS_SUFFIX = 'Handler';

	/**
	 * @access public
	 * @param mixed $criteria 抽出条件
	 * @param mixed $order ソート順
	 */
	public function __construct ($criteria = null, $order = null) {
		$this->setCriteria($criteria);
		$this->setOrder($order);

		if (!$this->isExists() && $this->getSchema()) {
			$this->create();
		}
	}

	/**
	 * 出力フィールド文字列を返す
	 *
	 * @access public
	 * @return string 出力フィールド文字列
	 */
	public function getFields () {
		return $this->fields;
	}

	/**
	 * 出力フィールド文字列を設定
	 *
	 * @access public
	 * @param mixed $fields 配列または文字列による出力フィールド
	 */
	public function setFields ($fields) {
		if ($fields) {
			$this->fields = BSSQL::getFieldsString($fields);
			$this->setExecuted(false);
		}
	}

	/**
	 * 主キーフィールド名を返す
	 *
	 * @access public
	 * @return string 主キーフィールド名
	 */
	public function getKeyField () {
		return $this->key;
	}

	/**
	 * 主キーフィールドを設定
	 *
	 * @access public
	 * @param string $key 主キーフィールド
	 */
	public function setKeyField ($key) {
		$this->key = $key;
	}

	/**
	 * 抽出条件文字列を返す
	 *
	 * @access public
	 * @return string 抽出条件文字列
	 */
	public function getCriteria () {
		return $this->criteria;
	}

	/**
	 * 抽出条件文字列を設定
	 *
	 * @access public
	 * @param mixed $criteria 配列または文字列による抽出条件
	 */
	public function setCriteria ($criteria) {
		if (!$criteria) {
			return;
		}
		$this->criteria = BSSQL::getCriteriaString($criteria);
		$this->setExecuted(false);
	}

	/**
	 * 抽出条件文字列を返す
	 *
	 * getCriteriaのエイリアス
	 *
	 * @access public
	 * @return string 抽出条件文字列
	 * final
	 */
	final public function getWhere () {
		return $this->getCriteria();
	}

	/**
	 * 抽出条件文字列を設定
	 *
	 * setCriteriaのエイリアス
	 *
	 * @access public
	 * @param mixed $criteria 配列または文字列による抽出条件
	 * @final
	 */
	final public function setWhere ($criteria) {
		$this->setCriteria($criteria);
	}

	/**
	 * ソート順文字列を返す
	 *
	 * @access public
	 * @return string ソート順文字列
	 */
	public function getOrder () {
		if (!$this->order) {
			$this->setOrder($this->getKeyField());
		}
		return $this->order;
	}

	/**
	 * ソート順文字列を設定
	 *
	 * @access public
	 * @param mixed $order 配列または文字列によるソート順
	 */
	public function setOrder ($order) {
		if (!$order) {
			return;
		}
		$this->order = BSSQL::getOrderString($order);
		$this->setExecuted(false);
	}

	/**
	 * ページ番号を返す
	 *
	 * @access public
	 * @return integer ページ番号
	 */
	public function getPage () {
		return $this->page;
	}

	/**
	 * ページ番号を設定
	 *
	 * @access public
	 * @param integer $page ページ番号
	 */
	public function setPage ($page = null) {
		if (!$page) {
			//何もしない
		} else if ($this->getLastPage() < $page) {
			$page = $this->getLastPage();
		} else if ($page < 1) {
			$page = 1;
		}
		$this->page = $page;
		$this->setExecuted(false);
	}

	/**
	 * ページサイズを返す
	 *
	 * @access public
	 * @return integer ページサイズ
	 */
	public function getPageSize () {
		return $this->pagesize;
	}

	/**
	 * ページ番号を設定
	 *
	 * @access public
	 * @param integer $pagesize ページサイズ
	 */
	public function setPageSize ($pagesize) {
		if (1 < $pagesize) {
			$this->pagesize = $pagesize;
			$this->setExecuted(false);
		}
	}

	/**
	 * テーブルは存在するか？
	 *
	 * @access public
	 * @return boolean 存在するならTrue
	 */
	public function isExists () {
		return $this->getDatabase()->getTableNames()->isContain($this->getName());
	}

	/**
	 * データベースを返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		return BSDatabase::getInstance();
	}

	/**
	 * レコードを返す
	 *
	 * @access public
	 * @param mixed[] $key 検索条件
	 * @return BSRecord レコード
	 */
	public function getRecord ($key) {
		if (!BSArray::isArray($key)) {
			$key = array($this->getKeyField() => $key);
		}

		if ($this->isExecuted()) {
			foreach ($this->getResult() as $record) {
				$match = true;
				foreach ($key as $field => $value) {
					if ($record[$field] != $value) {
						$match = false;
					}
				}
				if ($match) {
					$class = $this->getRecordClassName();
					return new $class($this, $record);
				}
			}
		} else {
			$table = clone $this;
			$criteria = $this->getDatabase()->createCriteriaSet();
			$criteria->merge($this->getCriteria());
			foreach ($key as $field => $value) {
				$criteria->register($field, $value);
			}
			$table->setCriteria($criteria);
			if ($table->count() == 1) {
				$table->query();
				$class = $this->getRecordClassName();
				return new $class($this, $table->result[0]);
			}
		}
	}

	/**
	 * レコード追加
	 *
	 * @access public
	 * @param mixed[] $values 値
	 * @param integer $flags フラグのビット列
	 *   BSDatabase::WITH_LOGGING ログを残さない
	 * @return string レコードの主キー
	 */
	public function createRecord ($values, $flags = BSDatabase::WITH_LOGGING) {
		if (!$this->isInsertable()) {
			throw new BSDatabaseException('%sへのレコード挿入は許可されていません。', $this);
		}

		$query = BSSQL::getInsertQueryString($this->getName(), $values, $this->getDatabase());
		$this->getDatabase()->exec($query);
		if ($this->isAutoIncrement()) {
			$sequence = $this->getDatabase()->getSequenceName(
				$this->getName(),
				$this->getKeyField()
			);
			$id = $this->getDatabase()->lastInsertId($sequence);
		} else {
			$id = $values[$this->getKeyField()];
		}

		$this->setExecuted(false);
		if ($flags & BSDatabase::WITH_LOGGING) {
			$message = new BSStringFormat('%s(%d)を作成しました。');
			$message[] = BSTranslateManager::getInstance()->execute($this->getName());
			$message[] = $id;
			$this->getDatabase()->putLog($message);
		}

		return $id;
	}

	/**
	 * レコード追加
	 *
	 * createRecordのエイリアス
	 *
	 * @access public
	 * @param mixed[] $values 値
	 * @param integer $flags フラグのビット列
	 *   BSDatabase::WITH_LOGGING ログを残さない
	 * @return string レコードの主キー
	 * @final
	 */
	final public function insertRecord ($values, $flags = BSDatabase::WITH_LOGGING) {
		return $this->createRecord($values, $flags);
	}

	/**
	 * レコード追加可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isInsertable () {
		return false;
	}

	/**
	 * 最終レコードを返す
	 *
	 * @access public
	 * @return BSRecord レコード
	 */
	public function getLastRecord () {
		return $this->getIterator()->getLast();
	}

	/**
	 * 先頭レコードを返す
	 *
	 * @access public
	 * @return BSRecord レコード
	 */
	public function getFirstRecord () {
		return $this->getIterator()->getFirst();
	}

	/**
	 * 全消去
	 *
	 * @access public
	 */
	public function clear () {
		if (!$this->isClearable()) {
			throw new BSDatabaseException('%sのレコード全消去は許可されていません。', $this);
		}
		$this->getDatabase()->exec('DELETE FROM ' . $this->getName());
	}

	/**
	 * レコードの全消去が可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isClearable () {
		return false;
	}

	/**
	 * クエリーは実行されたか？
	 *
	 * @access protected
	 * @return boolean 実行されたならTrue
	 */
	protected function isExecuted () {
		return $this->executed;
	}

	/**
	 * クエリー実行フラグを設定
	 *
	 * @access protected
	 * @param boolean $executed クエリー実行フラグ
	 */
	protected function setExecuted ($executed) {
		if (!$this->executed = $executed) {
			$this->queryString = null;
			$this->result = array();
		}
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSTableIterator イテレータ
	 */
	public function getIterator () {
		return new BSTableIterator($this);
	}

	/**
	 * テーブルを作成
	 *
	 * @access public
	 */
	public function create () {
		if ($this->isExists()) {
			throw new BSDatabaseException('%sは既に存在します。', $this);
		}
		if ($schema = $this->getSchema()) {
			$this->getDatabase()->exec(
				BSSQL::getCreateTableQueryString($this->getName(), $schema->getParameters())
			);
		}
	}

	/**
	 * 内容を返す
	 *
	 * getResultのエイリアス
	 *
	 * @access public
	 * @return string[] 結果の配列
	 * @final
	 */
	final public function getContents () {
		return $this->getResult();
	}

	/**
	 * 結果を返す
	 *
	 * @access public
	 * @return string[] 結果の配列
	 */
	public function getResult () {
		if (!$this->isExecuted()) {
			$this->query();
		}
		return $this->result;
	}

	/**
	 * クエリーを送信して結果を返す
	 *
	 * @access public
	 * @return string[] 結果の配列
	 */
	public function query () {
		$this->queryString = null;
		$this->result = BSString::convertEncoding(
			$this->getDatabase()->query($this->getQueryString())->fetchAll(),
			'utf-8',
			$this->getDatabase()->getEncoding()
		);
		$this->setExecuted(true);
		return $this->result;
	}

	/**
	 * レコード数を返す
	 *
	 * @access public
	 * @return integer レコード数
	 */
	public function count () {
		if (!$this->getPage()) {
			return $this->countAll();
		}
		return count($this->getResult());
	}

	/**
	 * 全てのレコード数を返す
	 *
	 * ページングしていても、全てのレコード数を返す。
	 *
	 * @access public
	 * @return integer 全てのレコード数
	 */
	public function countAll () {
		$sql = BSSQL::getSelectQueryString(
			'count(*) AS cnt',
			$this->getName(),
			$this->getCriteria()
		);
		$row = $this->getDatabase()->query($sql)->fetch();
		return $row['cnt'];
	}

	/**
	 * クエリー文字列を返す
	 *
	 * @access public
	 * @return string クエリー文字列
	 */
	public function getQueryString () {
		if (!$this->queryString) {
			if ($this->getPage()) {
				$this->queryString = BSSQL::getSelectQueryString(
					$this->getFields(),
					$this->getName(),
					$this->getCriteria(),
					$this->getOrder(),
					null,
					$this->getPage(),
					$this->getPageSize()
				);
			} else {
				$this->queryString = BSSQL::getSelectQueryString(
					$this->getFields(),
					$this->getName(),
					$this->getCriteria(),
					$this->getOrder()
				);
			}
		}
		return $this->queryString;
	}

	/**
	 * ページ数を返す
	 *
	 * @access public
	 * @return integer ページ数
	 */
	public function getLastPage () {
		if (!$this->lastpage) {
			if ($page = ceil($this->countAll() / $this->getPageSize())) {
				$this->lastpage = $page;
			} else {
				$this->lastpage = 1;
			}
		}
		return $this->lastpage;
	}

	/**
	 * 最終ページか？
	 *
	 * @access public
	 * @return boolean 最終ページならTrue
	 */
	public function isLastPage () {
		return $this->getPage() == $this->getLastPage();
	}

	/**
	 * 現在の抽出条件で抽出して、配列で返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string[] ラベルの配列
	 */
	public function getLabels ($language = 'ja') {
		$labels = array();
		foreach ($this as $record) {
			$labels[$record->getID()] = $record->getLabel($language);
		}
		return $labels;
	}

	/**
	 * フィールド名の配列を返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string[] フィールド名の配列
	 */
	public function getFieldNames ($language = 'ja') {
		if (!$this->fieldNames) {
			if ($result = $this->getResult()) {
				$translator = BSTranslateManager::getInstance();
				foreach ($result[0] as $key => $value) {
					$this->fieldNames[$key] = $translator->execute($key, $language);
				}
			}
		}
		return $this->fieldNames;
	}

	/**
	 * 全ての主キーを返す
	 *
	 * @access public
	 * @return BSArray 主キーの配列
	 */
	public function getIDs () {
		if (!$this->ids) {
			$this->ids = new BSArray;
			$sql = BSSQL::getSelectQueryString(
				$this->getKeyField(),
				$this->getName(),
				$this->getCriteria(),
				$this->getOrder(),
				$this->getKeyField()
			);
			foreach ($this->getDatabase()->query($sql) as $row) {
				$this->ids[] = $row[$this->getKeyField()];
			}
		}
		return $this->ids;
	}

	/**
	 * 更新日付を返す
	 *
	 * @access public
	 * @return BSDate 更新日付
	 */
	public function getUpdateDate () {
		$date = null;
		foreach ($this as $record) {
			if (!$date || ($date < $record->getUpdateDate())) {
				$date = $record->getUpdateDate();
			}
		}
		return $date;
	}

	/**
	 * テーブル名を返す
	 *
	 * @access public
	 * @return string テーブル名
	 */
	public function getName () {
		if (!$this->name) {
			$this->name = BSString::underscorize($this->getRecordClassName());
		}
		return $this->name;
	}

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClassName () {
		if (!$this->recordClassName) {
			$pattern = '/^([A-Za-z]+)' . self::CLASS_SUFFIX . '$/';
			if (preg_match($pattern, get_class($this), $matches)) {
				$this->recordClassName = BSClassLoader::getInstance()->getClassName($matches[1]);
			} else {
				throw new BSDatabaseException(
					'"%s"のクラス名が正しくありません。', get_class($this)
				);
			}
		}
		return $this->recordClassName;
	}

	/**
	 * オートインクリメントのテーブルか？
	 *
	 * @access public
	 * @return boolean オートインクリメントならTrue
	 */
	public function isAutoIncrement () {
		return false;
	}

	/**
	 * ディレクトリを返す
	 *
	 * @access public
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory () {
		try {
			return BSController::getInstance()->getDirectory($this->getName());
		} catch (BSFileException $e) {
		}
	}

	/**
	 * 翻訳して返す
	 *
	 * @access public
	 * @param string $label ラベル
	 * @param string $language 言語
	 * @return string 翻訳された文字列
	 */
	public function translate ($label, $language) {
		if ($record = $this->getRecord($label)) {
			return $record->getLabel($language);
		}
	}

	/**
	 * 辞書の名前を返す
	 *
	 * @access public
	 * @return string 辞書の名前
	 */
	public function getDictionaryName () {
		return get_class($this);
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->getLabels();
	}

	/**
	 * スキーマを返す
	 *
	 * @access public
	 * @return BSArray フィールド情報の配列
	 */
	public function getSchema () {
		return null;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		try {
			$word = BSTranslateManager::getInstance()->execute($this->getName());
		} catch (BSTranslateException $e) {
			$word = $this->getName();
		}
		return $word . 'テーブル';
	}

	/**
	 * テーブルハンドラを返す
	 *
	 * @access public
	 * @param string $class レコード用クラス名、又はテーブル名
	 * @return BSTableHandler テーブルハンドラ
	 * @static
	 */
	static public function getInstance ($class) {
		$table = BSClassLoader::getInstance()->getObject($class, self::CLASS_SUFFIX);
		if (($table instanceof BSTableHandler) == false) {
			throw new BSDatabaseException('"%s" はテーブルハンドラではありません。', $class);
		}
		return $table;
	}

	/**
	 * 全ステータスを返す
	 *
	 * @access public
	 * @param mixed[] $values 値
	 * @static
	 */
	static public function getStatusOptions () {
		return BSTranslateManager::getInstance()->getHash(
			array('show', 'hide')
		);
	}
}

/* vim:set tabstop=4: */
