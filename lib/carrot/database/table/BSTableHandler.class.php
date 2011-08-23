<?php
/**
 * @package org.carrot-framework
 * @subpackage database.table
 */

/**
 * データベーステーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSTableHandler implements IteratorAggregate, BSDictionary, BSAssignable {
	private $fields;
	private $criteria;
	private $order;
	private $page;
	private $pagesize = 20;
	private $lastpage;
	private $executed = false;
	private $result = array();
	private $queryString;
	private $recordClass;
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
		$this->fields = new BSTableFieldSet;
		$this->criteria = $this->createCriteriaSet();
		$this->order = new BSTableFieldSet;
		$this->setCriteria($criteria);
		$this->setOrder($order);
		$this->setFields('*');

		if (!$this->isExists() && $this->getSchema()) {
			$this->createTable();
		}
	}

	/**
	 * @access public
	 */
	public function __clone () {
		$this->fields = clone $this->fields;
		$this->criteria = clone $this->criteria;
		$this->order = clone $this->order;
		if ($this->ids) {
			$this->ids = clone $this->ids;
		}
	}

	/**
	 * 出力フィールド文字列を返す
	 *
	 * @access public
	 * @return BSTableFieldSet 出力フィールド
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
		if (!$fields) {
			return;
		}
		if ($fields instanceof BSTableFieldSet) {
			$fields = clone $fields;
		} else {
			$fields = new BSTableFieldSet($fields);
		}
		$this->fields = $fields;
		$this->setExecuted(false);
	}

	/**
	 * 主キーフィールド名を返す
	 *
	 * @access public
	 * @return string 主キーフィールド名
	 */
	public function getKeyField () {
		return 'id';
	}

	/**
	 * 状態フィールド名
	 *
	 * @access public
	 * @return string 状態フィールド名
	 */
	public function getStatusField () {
		return 'status';
	}

	/**
	 * 作成日フィールド名
	 *
	 * @access public
	 * @return string 作成日フィールド名
	 */
	public function getCreateDateField () {
		return 'create_date';
	}

	/**
	 * 更新日フィールド名
	 *
	 * @access public
	 * @return string 更新日フィールド名
	 */
	public function getUpdateDateField () {
		return 'update_date';
	}

	/**
	 * 抽出条件を返す
	 *
	 * @access public
	 * @return BSCriteriaSet 抽出条件
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
		if ($criteria instanceof BSCriteriaSet) {
			$criteria = clone $criteria;
		} else {
			$criteria = new BSCriteriaSet($criteria);
		}
		$criteria->setDatabase($this->getDatabase());
		$this->criteria = $criteria;
		$this->setExecuted(false);
	}

	/**
	 * ソート順文字列を返す
	 *
	 * @access public
	 * @return BSFieldSet ソート順文字列
	 */
	public function getOrder () {
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
		if ($order instanceof BSTableFieldSet) {
			$order = clone $order;
		} else {
			$order = new BSTableFieldSet($order);
		}
		if (!$order->count()) {
			$order[] = $this->getKeyField();
		}
		$this->order = $order;
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
	 * 上位のレコードを返す
	 *
	 * @access public
	 * @param integer $limit 件数
	 * @return BSTableHandler 上位のレコード
	 */
	public function getRecent ($limit) {
		$table = clone $this;
		$table->setPage(1);
		$table->setPageSize($limit);
		return $table;
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
	 * 抽出条件を生成して返す
	 *
	 * @access protected
	 * @return BSCriteriaSet 抽出条件
	 */
	protected function createCriteriaSet () {
		return $this->getDatabase()->createCriteriaSet();
	}

	/**
	 * レコードを返す
	 *
	 * @access public
	 * @param mixed[] $key 検索条件
	 * @return BSRecord レコード
	 */
	public function getRecord ($key) {
		if (is_array($key) || ($key instanceof BSParameterHolder)) {
			$key = new BSArray($key);
		} else {
			$key = new BSArray(array($this->getKeyField() => $key));
		}

		$class = $this->getRecordClass();
		$record = new $class($this);
		if ($record->isSerializable() && ($cache = $this->getRecordCache($record, $key))) {
			return $record->initialize($cache);
		} else if ($this->isExecuted()) {
			foreach ($this->getResult() as $row) {
				foreach ($key as $field => $value) {
					if ($row[$field] != $value) {
						continue 2;
					}
				}
				return $record->initialize($row);
			}
		} else {
			$table = clone $this;
			foreach ($key as $field => $value) {
				$table->getCriteria()->register($field, $value);
			}
			if ($table->count() == 1) {
				$table->query();
				return $record->initialize($table->result[0]);
			}
		}
	}
	private function getRecordCache (BSRecord $record, BSArray $key) {
		if ($id = $key[$this->getKeyField()]) {
			$name = sprintf('%s.%08d', get_class($record), $id);
			if ($data = BSController::getInstance()->getAttribute($name)) {
				return $data['_attributes'];
			}
		}
	}

	/**
	 * レコード追加
	 *
	 * @access public
	 * @param mixed $values 値
	 * @param integer $flags フラグのビット列
	 *   BSDatabase::WITH_LOGGING ログを残さない
	 * @return string レコードの主キー
	 */
	public function createRecord ($values, $flags = null) {
		if (!$this->isInsertable()) {
			throw new BSDatabaseException($this . 'へのレコード挿入はできません。');
		}

		$fields = $this->getProfile()->getFields();
		foreach (array($this->getCreateDateField(), $this->getUpdateDateField()) as $field) {
			if ($fields[$field]) {
				$values[$field] = BSDate::getNow('Y-m-d H:i:s');
			}
		}

		$db = $this->getDatabase();
		$db->exec(BSSQL::getInsertQueryString($this, $values, $db));
		if ($this->hasSurrogateKey()) {
			$id = $db->lastInsertId($db->getSequenceName($this->getName(), $this->getKeyField()));
		} else {
			$id = $values[$this->getKeyField()];
		}
		$this->setExecuted(false);
		if (!($flags & BSDatabase::WITHOUT_LOGGING)) {
			$message = new BSStringFormat('%s(%s)を作成しました。');
			$message[] = BSTranslateManager::getInstance()->execute($this->getName());
			$message[] = $id;
			$db->log($message);
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
	final public function insertRecord ($values, $flags = null) {
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
	 * サロゲートキーを持つテーブルか？
	 *
	 * @access protected
	 * @return boolean サロゲートキーを持つならTrue
	 */
	protected function hasSurrogateKey () {
		return $this->isInsertable();
	}

	/**
	 * 全消去
	 *
	 * @access public
	 */
	public function clear () {
		if (!$this->isClearable()) {
			throw new BSDatabaseException($this . 'のレコード全消去はできません。');
		}
		$this->getDatabase()->exec('DELETE FROM ' . $this->getName());

		if ($dir = $this->getDirectory()) {
			$dir->clear();
		}
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
	 * @access public
	 * @return BSTableIterator イテレータ
	 */
	public function getIterator () {
		return new BSTableIterator($this);
	}

	/**
	 * テーブルを作成
	 *
	 * @access protected
	 */
	protected function createTable () {
		if ($this->isExists()) {
			throw new BSDatabaseException($this . 'は既に存在します。');
		}
		if ($schema = $this->getSchema()) {
			$this->getDatabase()->createTable($this->getName(), $schema);
		}
	}

	/**
	 * プロフィールを返す
	 *
	 * @access public
	 * @return BSTableProfile
	 */
	public function getProfile () {
		return $this->getDatabase()->getTableProfile($this->getName());
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
		$this->result = $this->getDatabase()->query($this->getQueryString())->fetchAll();
		$this->setExecuted(true);
		return $this->result;
	}

	/**
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
			$this->name = BSString::underscorize($this->getRecordClass());
		}
		return $this->name;
	}

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClass () {
		if (!$this->recordClass) {
			$class = get_class($this);
			if (mb_ereg('^([[:alpha:]]+)' . self::CLASS_SUFFIX . '$', $class, $matches)) {
				$this->recordClass = BSClassLoader::getInstance()->getClass($matches[1]);
			} else {
				throw new BSDatabaseException($class . 'のクラス名が正しくありません。');
			}
		}
		return $this->recordClass;
	}

	/**
	 * ディレクトリを返す
	 *
	 * @access public
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory () {
		try {
			return BSFileUtility::getDirectory($this->getName());
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
	public function getAssignableValues () {
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
	 * 子クラスを返す
	 *
	 * @access public
	 * @return BSArray 子クラス名の配列
	 * @static
	 */
	public function getChildClasses () {
		return new BSArray;
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
	 * 画像のサイズ名を全てを返す
	 *
	 * @access public
	 * @return BSArray 画像のサイズ名
	 * @static
	 */
	static public function getImageNames () {
		return new BSArray;
	}

	/**
	 * 添付ファイル名を全てを返す
	 *
	 * @access public
	 * @return BSArray 添付ファイル名
	 * @static
	 */
	static public function getAttachmentNames () {
		return new BSArray;
	}

	/**
	 * テーブルハンドラを生成して返す
	 *
	 * @access public
	 * @param string $class レコード用クラス名、又はテーブル名
	 * @return BSTableHandler テーブルハンドラ
	 * @static
	 */
	static public function create ($class) {
		$table = BSClassLoader::getInstance()->getObject($class, self::CLASS_SUFFIX);
		if (!($table instanceof BSTableHandler)) {
			throw new BSDatabaseException($class . 'はテーブルハンドラではありません。');
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
