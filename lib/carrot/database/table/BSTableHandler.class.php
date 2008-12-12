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
abstract class BSTableHandler implements IteratorAggregate, BSDictionary {
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
	const WITH_PAGING = true;
	const WITHOUT_PAGING = false;

	/**
	 * @access public
	 * @param string $criteria 抽出条件
	 * @param string $order ソート順
	 */
	public function __construct ($criteria = null, $order = null) {
		$this->setCriteria($criteria);
		$this->setOrder($order);
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		switch ($method) {
			case ('get' . $this->getRecordClassName()):
				return $this->getRecord($values[0]);
			case ('create' . $this->getRecordClassName()):
				return $this->createRecord($values[0]);
			default:
				throw new BSMagicMethodException('仮想メソッド"%s"は未定義です。', $method);
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
		if ($criteria) {
			$this->criteria = BSSQL::getCriteriaString($criteria);
			$this->setExecuted(false);
		}
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
		if ($order) {
			$this->order = BSSQL::getOrderString($order);
			$this->setExecuted(false);
		}
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
	 * @param mixed[] $primaryKey 検索条件
	 * @return BSRecord レコード
	 */
	public function getRecord ($primaryKey) {
		if (!BSArray::isArray($primaryKey)) {
			$primaryKey = array($this->getKeyField() => $primaryKey);
		}

		if ($this->isExecuted()) {
			foreach ($this->getResult() as $record) {
				$match = true;
				foreach ($primaryKey as $field => $value) {
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
			$criteria = array();
			foreach ($primaryKey as $field => $value) {
				$criteria[] = $field . '=' . $this->getDatabase()->quote($value);
			}
			$table->setCriteria($criteria);
			if ($table->count() == 1) {
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
	 * @param integer $flag フラグのビット列
	 * @return string レコードの主キー
	 */
	public function createRecord ($values, $flag = BSDatabase::WITH_LOGGING) {
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
		if ($flag & BSDatabase::WITH_LOGGING) {
			$name = BSTranslateManager::getInstance()->execute($this->getName());
			$this->getDatabase()->putLog(sprintf('%s(%s)を作成しました。', $name, $id));
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
	 * @param integer $flag フラグのビット列
	 * @return string レコードの主キー
	 * @final
	 */
	final public function insertRecord ($values, $flag = BSDatabase::WITH_LOGGING) {
		return $this->createRecord($values, $flag);
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
	 * レコード追加可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isInsertable () {
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
	 * @param boolean $mode ページングされたレコード数を返すか？
	 * @return integer レコード数
	 */
	public function getRecordCount ($mode = self::WITH_PAGING) {
		if ($mode == self::WITHOUT_PAGING) {
			$sql = BSSQL::getSelectQueryString(
				'count(*) AS cnt',
				$this->getName(),
				$this->getCriteria()
			);
			$row = $this->getDatabase()->query($sql)->fetch();
			return $row['cnt'];
		} else {
			return count($this->getResult());
		}
	}

	/**
	 * レコード数を返す
	 *
	 * getRecordCountのエイリアス
	 *
	 * @access public
	 * @param boolean $mode ページングされたレコード数を返すか？
	 * @return integer レコード数
	 * @final
	 */
	final public function count ($mode = self::WITH_PAGING) {
		return $this->getRecordCount();
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
	 * レコードは存在するか？
	 *
	 * @access public
	 * @param string[] $criteria 検索条件
	 */
	public function isExists ($criteria) {
		$table = clone $this;
		$table->setFields($this->getKeyField());
		$table->setCriteria($criteria);
		return (0 < $table->count());
	}

	/**
	 * レコードは存在するか？
	 *
	 * isExistsのエイリアス
	 *
	 * @access public
	 * @param string[] $criteria 検索条件
	 * @final
	 */
	final public function isExist ($criteria) {
		return self::isExists($criteria);
	}

	/**
	 * ページ数を返す
	 *
	 * @access public
	 * @return integer ページ数
	 */
	public function getLastPage () {
		if (!$this->lastpage) {
			if ($page = ceil($this->count(self::WITHOUT_PAGING) / $this->getPageSize())) {
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
			foreach ($this as $record) {
				$this->ids[] = $record->getID();
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
			if (preg_match('/^([A-Za-z]+)Handler$/', get_class($this), $matches)) {
				$this->recordClassName = $matches[1];
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
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('テーブル "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
