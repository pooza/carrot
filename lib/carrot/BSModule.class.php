<?php
/**
 * @package org.carrot-framework
 */

/**
 * モジュール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSModule implements BSHTTPRedirector {
	private $directories;
	private $actions;
	private $attributes;
	private $config = array();
	private $configFiles;
	private $prefix;
	private $record;
	private $table;
	private $parameters;
	private $recordClassName;
	static private $instances;
	static private $prefixes = array();

	/**
	 * @access private
	 * @param string $name モジュール名
	 */
	private function __construct ($name) {
		$this->getAttributes()->setParameter('name', $name);

		if (!$this->getDirectory()) {
			throw new BSFileException('%sのディレクトリが見つかりません。', $this);
		}

		if ($file = $this->getConfigFile('module')) {
			$config = array();
			require(BSConfigManager::getInstance()->compile($file));
			$this->config += (array)$config;
			$this->getAttributes()->setParameters($config['module']);
		}

		if ($file = $this->getConfigFile('filters')) {
			$this->config += $file->getResult();
		}
	}

	/**
	 * フライウェイトインスタンスを返す
	 *
	 * @access public
	 * @param string $name モジュール名
	 * @static
	 */
	static public function getInstance ($name) {
		if (!self::$instances) {
			self::$instances = new BSArray;
		}

		$name = BSString::stripControlCharacters($name);
		if (!self::$instances[$name]) {
			self::$instances[$name] = new BSModule($name);
		}
		return self::$instances[$name];
	}

	/**
	 * モジュール名を返す
	 *
	 * @access public
	 * @return string モジュール名
	 */
	public function getName () {
		return $this->getAttributes()->getParameter('name');
	}

	/**
	 * 属性値を全て返す
	 *
	 * @access public
	 * @return BSArray 属性値
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = new BSArray;
		}
		return $this->attributes;
	}

	/**
	 * ディレクトリを返す
	 *
	 * @access public
	 * @param string $name ディレクトリ名
	 * @return BSDirectory 対象ディレクトリ
	 */
	public function getDirectory ($name = 'module') {
		if (!$this->directories) {
			$this->directories = new BSArray;
		}
		if (!$this->directories[$name]) {
			switch ($name) {
				case 'module':
					$dir = BSController::getInstance()->getDirectory('modules');
					$this->directories['module'] = $dir->getEntry($this->getName());
					break;
				default:
					$this->directories[$name] = $this->getDirectory('module')->getEntry($name);
					break;
			}
		}
		return $this->directories[$name];
	}

	/**
	 * 検索条件キャッシュを返す
	 *
	 * @access public
	 * @return BSArray 検索条件キャッシュ
	 */
	public function getParameterCache () {
		if (!$this->parameters) {
			$this->parameters = new BSArray;
			if ($params = BSUser::getInstance()->getAttribute($this->getParameterCacheName())) {
				$this->parameters->setParameters($params);
			}
		}
		return $this->parameters;
	}

	/**
	 * 検索条件キャッシュを設定
	 *
	 * @access public
	 * @param BSArray $params 検索条件キャッシュ
	 */
	public function setParameterCache (BSArray $params) {
		$this->parameters = clone $params;
		$this->parameters->removeParameter(BSController::MODULE_ACCESSOR);
		$this->parameters->removeParameter(BSController::ACTION_ACCESSOR);
		BSUser::getInstance()->setAttribute(
			$this->getParameterCacheName(),
			$this->parameters->getParameters()
		);
	}

	/**
	 * 検索条件キャッシュをクリア
	 *
	 * @access public
	 */
	public function clearParameterCache () {
		BSUser::getInstance()->removeAttribute($this->getParameterCacheName());
	}

	/**
	 * 検索条件キャッシュの属性名を返す
	 *
	 * @access private
	 * @return string 検索条件キャッシュの属性名
	 */
	private function getParameterCacheName () {
		return $this->getName() . 'Criteria';
	}

	/**
	 * テーブルを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブル
	 */
	public function getTable () {
		if (!$this->table) {
			$class = $this->getRecordClassName() . 'Handler';
			$this->table = new $class;
		}
		return $this->table;
	}

	/**
	 * 編集中レコードを返す
	 *
	 * @access public
	 * @return BSRecord 編集中レコード
	 */
	public function getRecord () {
		if (!$this->record && $this->getRecordID()) {
			$this->record = $this->getTable()->getRecord($this->getRecordID());
		}
		return $this->record;
	}

	/**
	 * カレントレコードIDを返す
	 *
	 * @access public
	 * @return integer カレントレコードID
	 */
	public function getRecordID () {
		return BSUser::getInstance()->getAttribute($this->getRecordIDName());
	}

	/**
	 * カレントレコードIDを設定
	 *
	 * @access public
	 * @param integer $id カレントレコードID
	 */
	public function setRecordID ($id) {
		if (BSArray::isArray($id)) {
			$id = new BSArray($id);
			$id = $id[$this->getTable()->getKeyField()];
		}
		BSUser::getInstance()->setAttribute($this->getRecordIDName(), $id);
	}

	/**
	 * カレントレコードIDをクリア
	 *
	 * @access public
	 */
	public function clearRecordID () {
		BSUser::getInstance()->removeAttribute($this->getRecordIDName());
	}

	/**
	 * カレントレコードIDの属性名を返す
	 *
	 * @access private
	 * @return string カレントレコードIDの属性名
	 */
	private function getRecordIDName () {
		return $this->getName() . 'ID';
	}

	/**
	 * モジュールフィルタをフィルタチェーンに加える
	 *
	 * @access private
	 * @param BSFilterChain $finterChain フィルタチェーン
	 */
	public function loadFilters (BSFilterChain $filterChain) {
		if ($file = $this->getConfigFile('filters')) {
			$objects = array();
			require(BSConfigManager::getInstance()->compile($file));
			if ($objects) {
				foreach ($objects as $filter) {
					$filterChain->register($filter);
				}
			}
		}
    }

	/**
	 * 設定ファイルを返す
	 *
	 * @access private
	 * @param string $name ファイル名
	 * @return BSConfigFile 設定ファイル
	 */
	private function getConfigFile ($name = 'module') {
		if (!$this->configFiles) {
			$this->configFiles = new BSArray;
		}
		if (!$this->configFiles[$name] && $this->getDirectory('config')) {
			$this->configFiles[$name] = BSConfigManager::getConfigFile(
				$this->getDirectory('config')->getPath() . DIRECTORY_SEPARATOR . $name
			);
		}
		return $this->configFiles[$name];
	}

	/**
	 * 設定ファイルを返す
	 *
	 * getConfigFileのエイリアス
	 *
	 * @access private
	 * @param string $name ファイル名
	 * @return BSConfigFile 設定ファイル
	 * @final
	 */
	final private function getIniFile ($name = 'module') {
		return $this->getConfigFile($name);
	}

	/**
	 * 設定値を返す
	 *
	 * @access public
	 * @param string $key キー名
	 * @param string $section セクション名
	 * @return string 設定値
	 */
	public function getConfig ($key, $section = 'module') {
		if (isset($this->config[$section][$key])) {
			return $this->config[$section][$key];
		}
	}

	/**
	 * バリデーション設定ファイルを返す
	 *
	 * @access public
	 * @param string $name 設定ファイルの名前
	 * @return BSConfigFile バリデーション設定ファイル
	 */
	public function getValidationFile ($name) {
		if (!$dir = $this->getDirectory('validate')) {
			return null;
		}
		return BSConfigManager::getConfigFile($dir->getPath() . DIRECTORY_SEPARATOR . $name);
	}

	/**
	 * アクションを返す
	 *
	 * @access public
	 * @param string $name アクション名
	 * @return BSAction アクション
	 */
	public function getAction ($name) {
		$name = BSString::stripControlCharacters($name);
		$class = $name . 'Action';
		if (!$dir = $this->getDirectory('actions')) {
			throw new BSFileException('%sにアクションディレクトリがありません。', $this);
		} else if (!$file = $dir->getEntry($class . '.class.php')) {
			throw new BSFileException('%sにアクション "%s" がありません。', $this, $name);
		}

		if (!$this->actions) {
			$this->actions = new BSArray;
		}
		if (!$this->actions[$name]) {
			require($file->getPath());
			$this->actions[$name] = new $class($this);
		}

		return $this->actions[$name];
	}

	/**
	 * クレデンシャルを返す
	 *
	 * @access public
	 * @return string クレデンシャル
	 */
	public function getCredential () {
		if ($file = $this->getConfigFile('filters')) {
			foreach ($file->getResult() as $section) {
				if (isset($section['class']) && ($section['class'] == 'BSSecurityFilter')) {
					if (isset($section['params']['credential'])) {
						return $section['params']['credential'];
					} else if (isset($section['param.credential'])) {
						return $section['param.credential'];
					}
				}
			}
		}
		return $this->getPrefix();
	}

	/**
	 * モジュール名プレフィックスを返す
	 *
	 * @access public
	 * @return string モジュール名プレフィックス
	 */
	public function getPrefix () {
		if (!$this->prefix) {
			$pattern = sprintf('/^(%s)/', self::getPrefixes()->join('|'));
			if (preg_match($pattern, $this->getName(), $matches)) {
				$this->prefix = $matches[1];
			}
		}
		return $this->prefix;
	}

	/**
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		$url = new BSURL;
		$url->setAttribute('path', sprintf('/%s/', $this->getName()));
		return $url;
	}

	/**
	 * レコードクラス名を返す
	 *
	 * @access public
	 * @return string レコードクラス名
	 */
	public function getRecordClassName () {
		if (!$this->recordClassName) {
			if (!$this->recordClassName = $this->getConfig('record_class')) {
				$pattern = sprintf('/^(%s)([A-Z][A-Za-z]+)$/', self::getPrefixes()->join('|'));
				if (preg_match($pattern, $this->getName(), $matches)) {
					$this->recordClassName = $matches[2];
				}
			}
		}
		return $this->recordClassName;
	}

	/**
	 * 全てのモジュール名プレフィックスを配列で返す
	 *
	 * @access public
	 * @return BSArray モジュール名プレフィックス
	 * @static
	 */
	static public function getPrefixes () {
		if (!self::$prefixes) {
			if ($prefixes = BSController::getInstance()->getConstant('MODULE_PREFIXES')) {
				self::$prefixes = BSString::explode(',', $prefixes);
			} else {
				self::$prefixes = new BSArray(array('Admin', 'Develop', 'User'));
			}
		}
		return self::$prefixes;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('モジュール "%s"', $this->getName());
	}
}

/* vim:set tabstop=4 ai: */
?>