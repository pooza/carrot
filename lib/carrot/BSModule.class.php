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
	private $url;
	private $parameters;
	private $recordClassName;
	static private $instances;
	static private $prefixes = array();

	/**
	 * @access protected
	 * @param string $name モジュール名
	 */
	protected function __construct ($name) {
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

		$name = BSString::stripControlCharacters($name);
		if (!self::$instances[$name]) {
			$module = new BSModule($name);
			$class = $name . 'Module';
			if ($file = $module->getDirectory()->getEntry($class . '.class.php')) {
				require($file->getPath());
				if (!class_exists($class)) {
					throw new BSInitializationException('"%s" が見つかりません。', $class);
				}
				$module = new $class($name);
			}
			self::$instances[$name] = $module;
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
					$dir = $this->controller->getDirectory('modules');
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
			throw new BSFileException('%sに "%s" がありません。', $this, $class);
		}

		if (!$this->actions) {
			$this->actions = new BSArray;
		}
		if (!$this->actions[$name]) {
			require($file->getPath());
			if (!class_exists($class)) {
				throw new BSInitializationException(
					'%sに "%s" が見つかりません。', $this, $class
				);
			}
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
		if (!$this->url) {
			$this->url = new BSCarrotURL;
			$this->url->setModuleName($this);
		}
		return $this->url;
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
	 * @return string レコードクラス名
	 */
	public function getRecordClassName () {
		if (!$this->recordClassName) {
			if (!$name = $this->getConfig('record_class')) {
				$pattern = sprintf('/^(%s)([A-Z][A-Za-z]+)$/', self::getPrefixes()->join('|'));
				if (preg_match($pattern, $this->getName(), $matches)) {
					$name = $matches[2];
				}
			}
			$this->recordClassName = BSString::stripControlCharacters($name);
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
