<?php
/**
 * @package org.carrot-framework
 * @subpackage controller
 */

/**
 * Carrotアプリケーションコントローラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSController {
	private $host;
	private $headers;
	const MODULE_ACCESSOR = 'm';
	const ACTION_ACCESSOR = 'a';

	/**
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
			default:
				throw new BSMagicMethodException('仮想プロパティ"%s"は未定義です。', $name);
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSController インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (php_sapi_name() == 'cli') {
			return BSConsoleController::getInstance();
		} else {
			return BSWebController::getInstance();
		}
	}

	/**
	 * ディスパッチ
	 *
	 * @access public
	 */
	public function dispatch () {
		if (!$module = $this->request[self::MODULE_ACCESSOR]) {
			$module = $this->getConstant('DEFAULT_MODULE');
		}
		if (!$action = $this->request[self::ACTION_ACCESSOR]) {
			$action = $this->getConstant('DEFAULT_ACTION');
		}

		try {
			$action = BSModule::getInstance($module)->getAction($action);
		} catch (Exception $e) {
			$action = $this->getNotFoundAction();
		}
		$action->forward();
	}

	/**
	 * 転送
	 *
	 * BSAction::forwardのエイリアス
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return string ビュー名
	 * @final
	 */
	final public function forwardTo (BSAction $action) {
		return $action->forward();
	}

	/**
	 * 転送
	 *
	 * Mojaviとの互換性の為のメソッド。
	 *
	 * @access public
	 * @param string $module モジュール名
	 * @param string $action アクション名
	 * @return string ビュー名
	 * @final
	 */
	final public function forward ($module, $action) {
		try {
			return BSModule::getInstance($module)->getAction($action)->forward();
		} catch (BSFileException $e) {
			return $this->getNotFoundAction()->forward();
		}
	}

	/**
	 * サーバ環境変数を返す
	 *
	 * @access public
	 * @param string $name サーバ環境変数の名前
	 * @return mixed サーバ環境変数
	 */
	public function getEnvironment ($name) {
		if (isset($_SERVER[$name])) {
			return $_SERVER[$name];
		}
	}

	/**
	 * 定数を返す
	 *
	 * @access public
	 * @param string $name 定数の名前
	 * @return string 定数の値
	 */
	public function getConstant ($name) {
		return BSConstantHandler::getInstance()->getParameter($name);
	}

	/**
	 * ログを出力
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	public function putLog ($message, $priority = BSLogger::DEFAULT_PRIORITY) {
		BSLogManager::getInstance()->put($message, $priority);
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
	 * サーバホストを返す
	 *
	 * @access public
	 * @return string サーバホスト
	 */
	public function getHost () {
		if (!$this->host) {
			$this->host = new BSHost($this->getEnvironment('SERVER_NAME'));
		}
		return $this->host;
	}

	/**
	 * リモートホストを返す
	 *
	 * BSRequest::getHostのエイリアス
	 *
	 * @access public
	 * @return string リモートホスト
	 * @final
	 */
	final public function getClientHost () {
		return $this->request->getHost();
	}

	/**
	 * サーバホストを返す
	 *
	 * getHostのエイリアス
	 *
	 * @access public
	 * @return string サーバホスト
	 * @final
	 */
	final public function getServerHost () {
		return $this->getHost();
	}

	/**
	 * UserAgentを返す
	 *
	 * BSRequest::getUserAgent()のエイリアス
	 *
	 * @access public
	 * @return BSUserAgent リモートホストのUserAgent
	 * @final
	 */
	final public function getUserAgent () {
		return $this->request->getUserAgent();
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
			return $this->getAction()->getModule();
		}
	}

	/**
	 * 呼ばれたアクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getAction () {
		return BSActionStack::getInstance()->getLastEntry();
	}

	/**
	 * セキュアアクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getSecureAction () {
		$module = $this->getConstant('SECURE_MODULE');
		$action = $this->getConstant('SECURE_ACTION');
		return BSModule::getInstance($module)->getAction($action);
	}

	/**
	 * NotFoundアクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getNotFoundAction () {
		$module = $this->getConstant('NOT_FOUND_MODULE');
		$action = $this->getConstant('NOT_FOUND_ACTION');
		return BSModule::getInstance($module)->getAction($action);
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
	 * @param BSDate $date 比較する日付 - この日付より古い属性値は破棄
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
	static public function getApplicationName ($lang = 'ja') {
		return BSTranslateManager::getInstance()->execute('APP_NAME', 'BSConstantHandler', $lang);
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
	final static public function getName ($lang = 'ja') {
		return self::getApplicationName($lang);
	}

	/**
	 * アプリケーションのバージョンを返す
	 *
	 * @access public
	 * @return string バージョン
	 */
	static public function getVersion () {
		return BSTranslateManager::getInstance()->execute('APP_VER', 'BSConstantHandler');
	}

	/**
	 * バージョン番号込みのアプリケーション名を返す
	 *
	 * @access public
	 * @param string $lang 言語
	 * @return string アプリケーション名
	 * @static
	 */
	static public function getFullApplicationName ($lang = 'ja') {
		return sprintf('%s %s', self::getName($lang), self::getVersion());
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
	final static public function getFullName ($lang = 'ja') {
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
	 * BSRequest::isCLI()のエイリアス
	 *
	 * @access public
	 * @return boolean コマンドライン環境ならTrue
	 * @final
	 */
	final public function isCLI () {
		return $this->request->isCLI();
	}

	/**
	 * SSL環境か？
	 *
	 * BSRequest::isSSL()のエイリアス
	 *
	 * @access public
	 * @return boolean SSL環境ならTrue
	 * @final
	 */
	final public function isSSL () {
		return $this->request->isSSL();
	}

	/**
	 * デバッグモードか？
	 *
	 * @access public
	 * @return boolean デバッグモードならTrue
	 */
	public function isDebugMode () {
		return $this->getConstant('DEBUG');
	}

	/**
	 * リゾルバは有効か？
	 *
	 * BSSocket::isResolvable()のエイリアス
	 *
	 * @access public
	 * @return boolean デバッグモードならTrue
	 * @final
	 */
	final public function isResolvable () {
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
	 * @param string $path インクルードするファイルのパス、又はBSFileオブジェクト
	 * @static
	 */
	static public function includeFile ($file) {
		if (($file instanceof BSFile) == false) {
			if (!BSUtility::isPathAbsolute($file)) {
				$file = self::getInstance()->getPath('lib') . DIRECTORY_SEPARATOR . $file;
			}
			$file = new BSFile($file);
		}
		if (!$file->isReadable()) {
			throw new BSFileException('"%s"はインクルード出来ません。', $file);
		}

		if ($config = ini_get('display_errors')) {
			ini_set('display_errors', 0);
		}
		require_once($file->getPath());
		if ($config) {
			ini_set('display_errors', 1);
		}
	}

	/**
	 * レスポンスヘッダを返す
	 *
	 * @access public
	 * @return BSArray レスポンスヘッダの配列
	 */
	public function getHeaders () {
		if (!$this->headers) {
			$this->headers = new BSArray;
		}
		return $this->headers;
	}

	/**
	 * レスポンスヘッダを設定
	 *
	 * @access public
	 * @param string $name フィールド名
	 * @param string $value フィールド値
	 */
	public function setHeader ($name, $value) {
		if (preg_match('/[[:cntrl:]]/', $name . $value)) {
			throw new BSHTTPException('レスポンスヘッダにコントロール文字が含まれています。');
		}
		$this->getHeaders()->setParameter($name, $value);
	}

	/**
	 * レスポンスヘッダを送信
	 *
	 * @access public
	 */
	public function putHeaders () {
	}	
}

/* vim:set tabstop=4 ai: */
?>