<?php
/**
 * @package org.carrot-framework
 * @subpackage module
 */

/**
 * モジュール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSModule implements BSHTTPRedirector, BSAssignable {
	protected $name;
	protected $title;
	protected $directories;
	protected $actions;
	protected $config = array();
	protected $configFiles;
	protected $prefix;
	protected $record;
	protected $table;
	protected $parameters;
	protected $recordClassName;
	static private $instances;
	static private $prefixes = array();

	/**
	 * @access protected
	 * @param string $name モジュール名
	 */
	protected function __construct ($name) {
		$this->name = $name;
		if (!$this->getDirectory()) {
			throw new BSModuleException($this . 'のディレクトリが見つかりません。');
		}
		if ($file = $this->getConfigFile('module')) {
			require(BSConfigManager::getInstance()->compile($file));
			$this->config = (array)$config;
		}
		if ($file = $this->getConfigFile('filters')) {
			$this->config['filters'] = $file->getResult();
		}
	}

	/**
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
				return BSController::getInstance();
			case 'request':
				return BSRequest::getInstance();
			case 'user':
				return BSUser::getInstance();
			default:
				throw new BSMagicMethodException('仮想プロパティ"%s"は未定義です。', $name);
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

		if (!self::$instances[$name]) {
			$module = new self($name);
			$class = $name . 'Module';
			if ($file = $module->getDirectory()->getEntry($class . '.class.php')) {
				require_once($file->getPath());
				$class = BSClassLoader::getInstance()->getClassName($class);
				$module = new $class($name);
			}
			self::$instances[$name] = $module;
		}
		return self::$instances[$name];
	}

	/**
	 * 属性値を全て返す
	 *
	 * @access public
	 * @return BSArray 属性値
	 */
	public function getAttributes () {
		return new BSArray(array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'title_menu' => $this->getMenuTitle(),
			'record_class' => $this->getRecordClassName(),
		));
	}

	/**
	 * モジュール名を返す
	 *
	 * @access public
	 * @return string モジュール名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		if (BSString::isBlank($this->title)) {
			if (BSString::isBlank($title = $this->getConfig('title'))) {
				if (BSString::isBlank($title = $this->getRecordClassName('ja'))) {
					$title = $this->getName();
				} else if ($this->isAdminModule()) {
					$title .= '管理';
				}
			}
			$this->title = mb_ereg_replace('モジュール$', '', $title) . 'モジュール';
		}
		return $this->title;
	}

	/**
	 * メニューでのタイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getMenuTitle () {
		if (BSString::isBlank($title = $this->getConfig('title_menu'))) {
			if (BSString::isBlank($title = $this->getRecordClassName('ja'))) {
				$title = $this->getName();
			}
		}
		return $title;
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
					$dir = BSFileUtility::getDirectory('modules');
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
			if ($params = $this->user->getAttribute($this->getParameterCacheName())) {
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
		$this->user->setAttribute(
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
		$this->user->removeAttribute($this->getParameterCacheName());
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
		if (!$this->table && !BSString::isBlank($class = $this->getRecordClassName())) {
			$this->table = BSTableHandler::getInstance($class);
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
		return $this->user->getAttribute($this->getRecordIDName());
	}

	/**
	 * カレントレコードIDを設定
	 *
	 * @access public
	 * @param integer $id カレントレコードID、又はレコード
	 */
	public function setRecordID ($id) {
		if ($id instanceof BSRecord) {
			$id = $id->getID();
		} else if (BSArray::isArray($id)) {
			$id = new BSArray($id);
			$id = $id[$this->getTable()->getKeyField()];
		}
		$this->user->setAttribute($this->getRecordIDName(), $id);
	}

	/**
	 * カレントレコードIDをクリア
	 *
	 * @access public
	 */
	public function clearRecordID () {
		$this->user->removeAttribute($this->getRecordIDName());
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
	 * パラメータ配列からレコードを返す
	 *
	 * パラメータが不足していたら、モジュールの情報で補い、検索する。
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSRecord レコード
	 */
	public function searchRecord (BSParameterHolder $params) {
		if (BSString::isBlank($params['class'])) {
			$params['class'] = $this->getRecordClassName();

			if (BSString::isBlank($params['id'])) {
				$params['id'] = $this->getRecord()->getID();
				return $this->getRecord();
			} 
		}

		if ($table = BSTableHandler::getInstance($params['class'])) {
			return $table->getRecord($params['id']);
		}
	}

	/**
	 * 設定ファイルを返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return BSConfigFile 設定ファイル
	 */
	public function getConfigFile ($name = 'module') {
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
		$class = $name . 'Action';
		if (!$dir = $this->getDirectory('actions')) {
			throw new BSModuleException($this . 'にアクションディレクトリがありません。');
		} else if (!$file = $dir->getEntry($class . '.class.php')) {
			throw new BSModuleException('%sに "%s" がありません。', $this, $class);
		}

		if (!$this->actions) {
			$this->actions = new BSArray;
		}
		if (!$this->actions[$name]) {
			require_once($file->getPath());
			$class = BSClassLoader::getInstance()->getClassName($class);
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
			$pattern = '^(' . self::getPrefixes()->join('|') . ')';
			if (mb_ereg($pattern, $this->getName(), $matches)) {
				$this->prefix = $matches[1];
			}
		}
		return $this->prefix;
	}

	/**
	 * 管理者向けモジュールか？
	 *
	 * @access public
	 * @return boolean 管理者向けモジュールならTrue
	 */
	public function isAdminModule () {
		return $this->getPrefix() == 'Admin';
	}

	/**
	 * リダイレクト対象
	 *
	 * URLを加工するケースが多い為、毎回生成する。
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		$url = BSURL::getInstance(null, 'BSCarrotURL');
		$url['module'] = $this;
		return $url;
	}

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function redirect () {
		return $this->getURL()->redirect();
	}

	/**
	 * レコードクラス名を返す
	 *
	 * @access public
	 * @param string $lang 言語 - 翻訳が必要な場合
	 * @return string レコードクラス名
	 */
	public function getRecordClassName ($lang = null) {
		if (!$this->recordClassName) {
			if (BSString::isBlank($name = $this->getConfig('record_class'))) {
				$pattern = '^' . $this->getPrefix() . '([[:upper:]][[:alpha:]]+)$';
				if (mb_ereg($pattern, $this->getName(), $matches)) {
					$name = $matches[1];
				}
			}
			if (!BSString::isBlank($name)) {
				try {
					$this->recordClassName = BSClassLoader::getInstance()->getClassName($name);
				} catch (Exception $e) {
					return null;
				}
			}
		}
		if (BSString::isBlank($lang)) {
			return $this->recordClassName;
		} else {
			$word = BSString::underscorize($this->recordClassName);
			return BSTranslateManager::getInstance()->execute($word);
		}
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->getAttributes()->getParameters();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('モジュール "%s"', $this->getName());
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
			self::$prefixes = BSString::explode(',', BS_MODULE_PREFIXES);
		}
		return self::$prefixes;
	}
}

/* vim:set tabstop=4: */

