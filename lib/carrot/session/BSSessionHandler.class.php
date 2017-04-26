<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage session
 */

/**
 * セッションハンドラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSessionHandler {
	use BSBasicObject;
	private $storage;
	protected $directory;

	/**
	 * @access public
	 */
	public function __construct () {
		session_write_close();
		ini_set('session.use_cookies', 1);
		ini_set('session.cookie_httponly', 1);
		ini_set('session.use_only_cookies', 1);
		ini_set('session.use_trans_sid', 0);
		ini_set('session.hash_function', 1);
		ini_set('session.use_strict_mode', 1);
		if (headers_sent() || !$this->getStorage()->initialize()) {
			throw new BSSessionException('セッションを開始できません。');
		}
		session_start();
	}

	/**
	 * セッションIDを返す
	 *
	 * @access public
	 * @return integer セッションID
	 */
	public function getID () {
		return session_id();
	}

	/**
	 * セッション名を返す
	 *
	 * @access public
	 * @return integer セッション名
	 */
	public function getName () {
		return session_name();
	}

	/**
	 * セッションIDを再生成
	 *
	 * @access public
	 */
	public function refresh () {
		session_regenerate_id(true);
	}

	/**
	 * セッションストレージを返す
	 *
	 * @access protected
	 * @return BSSessionStorage セッションストレージ
	 */
	protected function getStorage () {
		if (!$this->storage) {
			$this->storage = $this->loader->createObject(
				BS_SESSION_STORAGE,
				'SessionStorage'
			);
		}
		return $this->storage;
	}

	/**
	 * セッション変数を返す
	 *
	 * @access public
	 * @param string $key 変数名
	 * @return mixed セッション変数
	 */
	public function read ($key) {
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
	}

	/**
	 * セッション変数を書き込む
	 *
	 * @access public
	 * @param string $key 変数名
	 * @param mixed $value 値
	 */
	public function write ($key, $value) {
		if (is_array($value) || ($value instanceof BSParameterHolder)) {
			$value = new BSArray($value);
			$value = $value->decode();
		} else if ($value instanceof BSParameterHolder) {
			$value = $value->getParameters();
		}
		$_SESSION[$key] = $value;
	}

	/**
	 * セッション変数を削除
	 *
	 * @access public
	 * @param string $key 変数名
	 */
	public function remove ($key) {
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * セッションディレクトリを返す
	 *
	 * @access public
	 * @return BSDirectory セッションディレクトリ
	 */
	public function getDirectory () {
		if (!$this->directory) {
			if (!$this->directory = BSFileUtility::getDirectory('tmp')->getEntry($this->getID())) {
				$this->directory = BSFileUtility::getDirectory('tmp')->createDirectory($this->getID());
			}
		}
		return $this->directory;
	}
}

/* vim:set tabstop=4: */
