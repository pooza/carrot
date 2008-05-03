<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage controller
 */

/**
 * Carrotアプリケーションコントローラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSController {
	const MODULE_ACCESSOR = MO_MODULE_ACCESSOR;
	const ACTION_ACCESSOR = MO_ACTION_ACCESSOR;
	const DEFAULT_MODULE = MO_DEFAULT_MODULE;
	const DEFAULT_ACTION = MO_DEFAULT_ACTION;
	const NOT_FOUND_MODULE = MO_ERROR_404_MODULE;
	const NOT_FOUND_ACTION = MO_ERROR_404_ACTION;
	const MAX_FORWARDS = 20;
	private $useragent;

	/**
	 * プロパティ取得のオーバライド
	 *
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'request':
				return BSRequest::getInstance();
			case 'user':
				return BSUser::getInstance();
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSController インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (php_sapi_name() == 'cli') {
			return BSConsoleController::getInstance();
		} else {
			return BSWebController::getInstance();
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
	 * ディスパッチ
	 *
	 * @access public
	 */
	public function dispatch () {
		if (!$module = $this->request->getParameter(self::MODULE_ACCESSOR)) {
			$module = self::DEFAULT_MODULE;
		}
		if (!$action = $this->request->getParameter(self::ACTION_ACCESSOR)) {
			$action = self::DEFAULT_ACTION;
		}
		$this->forward($module, $action);
	}

	/**
	 * フォワード
	 *
	 * @access public
	 * @param string $module モジュール名
	 * @param string $action アクション名
	 */
	public function forward ($module, $action) {
		if (self::MAX_FORWARDS < BSActionStack::getInstance()->getSize()) {
			throw new ForwardException('フォワードが多すぎます。');
		}

		if (!MO_AVAILABLE) {
			$module = MO_UNAVAILABLE_MODULE;
			$action = MO_UNAVAILABLE_ACTION;
		}

		try {
			$module = BSModule::getInstance($module);
			$action = $module->getAction($action);
		} catch (BSFileException $e) {
			$module = BSModule::getInstance(self::NOT_FOUND_MODULE);
			$action = $module->getAction(self::NOT_FOUND_ACTION);
		}
		BSActionStack::getInstance()->addEntry($action);

		if (!$module->isEnabled()) {
			$this->forward(MO_MODULE_DISABLED_MODULE, MO_MODULE_DISABLED_ACTION);
			return;
		} else if (!$action->initialize()) {
			$message = sprintf(
				'Action initialization failed for module "%s", action "%s"',
				$module->getName(),
				$action->getName()
			);
			throw new InitializationException($message);
		}

		$filterChain = new FilterChain();
		if (MO_AVAILABLE) {
			if ($action->isSecure()) {
				$filter = new BSSecurityFilter();
				$filter->initialize();
				$filterChain->register($filter);
			}
			$this->loadFilters($filterChain);
			$module->loadFilters($filterChain);
		}
		$filter = new ExecutionFilter();
		$filter->initialize();
		$filterChain->register($filter);
		$filterChain->execute();
	}

	/**
	 * グローバルフィルタをフィルタチェーンに加える
	 *
	 * @access private
	 * @param FilterChain $finterChain フィルタチェーン
	 */
	private function loadFilters (FilterChain $filterChain) {
		$objects = array();
		require_once(ConfigCache::checkConfig('config/filters.ini'));
		if ($objects) {
			foreach ($objects as $filter) {
				$filterChain->register($filter);
			}
		}
	}

	/**
	 * PHPセッションIDを返す
	 *
	 * @access public
	 * @return string セッションID
	 */
	public function getSessionID () {
		return session_id();
	}

	/**
	 * サーバ環境変数を返す
	 *
	 * @access public
	 * @param string $name サーバ環境変数の名前
	 * @return mixed サーバ環境変数
	 */
	public function getEnvironment ($name) {
		return BSServerEnvironment::getInstance()->getAttribute($name);
	}

	/**
	 * 特別なディレクトリを返す
	 *
	 * @access public
	 * @param string $name ディレクトリの名前
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory ($name) {
		return BSDirectoryFinder::getInstance()->getDirectory($name);
	}


	/**
	 * 特別なディレクトリのパスを返す
	 *
	 * @access public
	 * @param string $name ディレクトリの名前
	 * @return string パス
	 */
	public function getPath ($name) {
		return BSDirectoryFinder::getInstance()->getPath($name);
	}

	/**
	 * Cookieを返す
	 *
	 * @access public
	 * @param string $name Cookieの名前
	 * @return string Cookieの値
	 */
	public function getCookie ($name) {
		return BSCookieHandler::getInstance()->getAttribute($name);
	}

	/**
	 * Cookieを設定する
	 *
	 * @access public
	 * @param string $name Cookieの名前
	 * @param string $value Cookieの値
	 */
	public function setCookie ($name, $value) {
		BSCookieHandler::getInstance()->setAttribute($name, $value);
	}

	/**
	 * Cookieを削除する
	 *
	 * @access public
	 * @param string $name Cookieの名前
	 */
	public function removeCookie ($name) {
		BSCookieHandler::getInstance()->removeAttribute($name);
	}

	/**
	 * クライアントホストを返す
	 *
	 * @access public
	 * @return string リモートホスト
	 */
	public function getClientHost () {
		return new BSHost($this->getEnvironment('REMOTE_ADDR'));
	}

	/**
	 * サーバホストを返す
	 *
	 * @access public
	 * @return string サーバホスト
	 */
	public function getServerHost () {
		return new BSHost($this->getEnvironment('SERVER_NAME'));
	}

	/**
	 * UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent リモートホストのUserAgent
	 */
	public function getUserAgent () {
		if (!$this->useragent) {
			if ($this->isDebugMode() && $this->request->hasParameter('ua')) {
				$name = $this->request->getParameter('ua');
			} else if ($this->isCLI()) {
				$name = 'Console';
			} else {
				$name = $this->getEnvironment('HTTP_USER_AGENT');
			}
			if (!$this->useragent = BSUserAgent::createInstance($name)) {
				throw new BSUserAgentException('サポートされていないUserAgentです。');
			}
		}
		return $this->useragent;
	}

	/**
	 * モジュールを返す
	 *
	 * @access public
	 * @param string $name モジュール名
	 * @return BSModule モジュール
	 */
	public function getModule ($name = null) {
		if ($name) {
			return BSModule::getInstance($name);
		} else {
			return BSActionStack::getInstance()->getLastEntry()->getModule();
		}
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		BSSerializeHandler::getInstance()->setAttribute($name, $value);
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		BSSerializeHandler::getInstance()->removeAttribute($name);
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param BSDate $date 比較する日付 - この日付より古い属性値は破棄する
	 * @return mixed 属性値
	 */
	public function getAttribute ($name, BSDate $date = null) {
		return BSSerializeHandler::getInstance()->getAttribute($name, $date);
	}

	/**
	 * アプリケーション名を返す
	 *
	 * @access public
	 * @param string $lang 言語
	 * @return string アプリケーション名
	 * @static
	 */
	public static function getApplicationName ($lang = 'ja') {
		return BSTranslator::getInstance()->translate('app_name', $lang);
	}

	/**
	 * アプリケーション名を返す
	 *
	 * getApplicationNameのエイリアス
	 *
	 * @access public
	 * @param string $lang 言語
	 * @return string アプリケーション名
	 * @final
	 */
	final public static function getName ($lang = 'ja') {
		return self::getApplicationName($lang);
	}

	/**
	 * バージョン番号込みのアプリケーション名を返す
	 *
	 * @access public
	 * @param string $lang 言語
	 * @return string アプリケーション名
	 * @static
	 */
	public static function getFullApplicationName ($lang = 'ja') {
		return sprintf(
			'%s %s',
			self::getApplicationName($lang),
			BSTranslator::getInstance()->translate('app_ver', $lang)
		);
	}

	/**
	 * バージョン番号込みのアプリケーション名を返す
	 *
	 * getFullApplicationNameのエイリアス
	 *
	 * @access public
	 * @param string $lang 言語
	 * @return string アプリケーション名
	 * @static
	 * @final
	 */
	final public static function getFullName ($lang = 'ja') {
		return self::getFullApplicationName($lang);
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		return BSSerializeHandler::getInstance()->getAttributes();
	}

	/**
	 * コマンドライン環境か？
	 *
	 * @access public
	 * @return boolean コマンドライン環境ならTrue
	 * @abstract
	 */
	abstract public function isCLI ();

	/**
	 * SSL環境か？
	 *
	 * @access public
	 * @return boolean SSL環境ならTrue
	 * @abstract
	 */
	abstract public function isSSL ();

	/**
	 * デバッグモードか？
	 *
	 * @access public
	 * @return boolean デバッグモードならTrue
	 */
	public function isDebugMode () {
		return defined('BS_DEBUG') && BS_DEBUG;
	}

	/**
	 * リゾルバは有効か？
	 *
	 * @access public
	 * @return boolean デバッグモードならTrue
	 */
	public function isResolvable () {
		return BSSocket::isResolvable();
	}

	/**
	 * タイムリミットを設定
	 *
	 * @access public
	 * @param integer $seconds 秒単位のタイムリミット
	 */
	public function setTimeLimit ($seconds) {
		return set_time_limit($seconds);
	}

	/**
	 * メモリリミットを設定
	 *
	 * @access public
	 * @param integer $size メモリサイズ
	 */
	public function setMemoryLimit ($size) {
		ini_set('memory_limit', $size);
	}

	/**
	 * エラーチェックなしでインクルード
	 *
	 * @access public
	 * @param string $file インクルードするファイル
	 * @static
	 */
	public static function includeLegacy ($file) {
		$file = new BSFile(self::getInstance()->getPath('lib') . $file);
		if (!$file->isReadable()) {
			throw new BSException('"%s"はインクルード出来ません。', $file);
		}
		@require_once($file->getPath());
	}

	/**
	 * ヘッダを送信
	 *
	 * @access public
	 * @param string $header ヘッダの内容
	 * @abstract
	 */
	abstract public function sendHeader ($header);
}

/* vim:set tabstop=4 ai: */
?>