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
abstract class BSAction {
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

	public function registerValidators ($validatorManager) {
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
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClassName () {
		if (!$this->recordClassName) {
			$prefixes = BSModule::getPrefixes();
			$pattern = sprintf('/^(%s)([A-Z][A-Za-z]+)$/', implode('|', $prefixes));
			if (preg_match($pattern, $this->controller->getModule()->getName(), $matches)) {
				$this->recordClassName = $matches[2];
			}
		}
		return $this->recordClassName;
	}

	public function getCredential () {
		return null;
	}

	public function getRequestMethods () {
		return BSRequest::GET | BSRequest::POST;
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