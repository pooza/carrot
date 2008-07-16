<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage action
 */

/**
 * アクション
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSAction implements BSHTTPRedirector {
	private $name;
	private $module;
	private $views;
	protected $recordClassName;

	public function __construct (BSModule $module) {
		$this->module = $module;
	}

	public function __get ($name) {
		switch ($name) {
			case 'controller':
				return BSController::getInstance();
			case 'request':
				return BSRequest::getInstance();
			case 'user':
				return BSUser::getInstance();
			case 'database':
				// $this->getTable()は使わない。
				if ($class = $this->getRecordClassName()) {
					$class .= 'Handler';
					if ($table = new $class) {
						return $table->getDatabase();
					}
				}
				return BSDatabase::getInstance();
		}
	}

	abstract public function execute ();

	public function initialize () {
		return true;
	}

	public function getDefaultView () {
		return BSView::SUCCESS;
	}

	public function handleError () {
		return BSView::ERROR;
	}

	public function registerValidators () {
	}

	public function validate () {
		return true;
	}

	public function isSecure () {
		return false;
	}

	/**
	 * アクション名を返す
	 *
	 * @access public
	 * @return string アクション名
	 */
	public function getName () {
		if (!$this->name) {
			preg_match('/^(.+)Action$/', get_class($this), $matches);
			$this->name = $matches[1];
		}
		return $this->name;
	}

	/**
	 * モジュールを返す
	 *
	 * @access public
	 * @return BSModule モジュール
	 */
	public function getModule () {
		return $this->module;
	}

	/**
	 * ビューを返す
	 *
	 * @access public
	 * @param string $name ビュー名
	 * @return BSView ビュー
	 */
	public function getView ($name) {
		$class = $this->getName() . $name . 'View';
		if (!$dir = $this->getModule()->getDirectory()->getEntry('views')) {
			throw new BSFileException('%sにビューディレクトリがありません。', $this->getModule());
		} else if (!$file = $dir->getEntry($class . '.class.php')) {
			throw new BSFileException(
				'%sに、ビュー "%s" がありません。',
				$this->getModule(),
				$class
			);
		}

		if (!$this->views) {
			$this->views = new BSArray;
		}
		if (!$this->views[$name]) {
			require_once($file->getPath());
			$this->views[$name] = new $class($this);
		}

		return $this->views[$name];
	}

	/**
	 * カレントレコードIDを返す
	 *
	 * @access public
	 * @return integer カレントレコードID
	 */
	public function getRecordID () {
		return null;
	}

	/**
	 * 編集中レコードを返す
	 *
	 * @access public
	 * @return BSRecord 編集中レコード
	 */
	public function getRecord () {
		return null;
	}

	/**
	 * テーブルを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブル
	 */
	public function getTable () {
		return null;
	}

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClassName () {
		if (!$this->recordClassName) {
			if (!$this->recordClassName = $this->getModule()->getConfig('record_class')) {
				$prefixes = BSModule::getPrefixes();
				$pattern = sprintf('/^(%s)([A-Z][A-Za-z]+)$/', implode('|', $prefixes));
				if (preg_match($pattern, $this->getModule()->getName(), $matches)) {
					$this->recordClassName = $matches[2];
				}
			}
		}
		return $this->recordClassName;
	}

	public function getCredential () {
		return null;
	}

	public function getRequestMethods () {
		return BSRequest::GET | BSRequest::POST | BSRequest::PUT | BSRequest::DELETE;
	}

	/**
	 * バリデーション設定ファイルを返す
	 *
	 * @access public
	 * @return BSConfigFile バリデーション設定ファイル
	 */
	public function getValidationFile () {
		return $this->getModule()->getValidationFile($this->getName());
	}

	/**
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		$url = new BSURL;
		$path = sprintf('/%s/%s', $this->getModule()->getName(), $this->getName());
		$url->setAttribute('path', $path);
		return $url;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('アクション "%s"', $this->getName());
	}
}

/* vim:set tabstop=4 ai: */
?>