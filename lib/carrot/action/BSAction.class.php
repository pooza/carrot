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
	protected $recordClassName;

	abstract public function execute ();

	public function initialize () {
		return true;
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

	public function getDefaultView () {
		return BSView::SUCCESS;
	}

	public function handleError () {
		return BSView::ERROR;
	}

	public function registerValidators ($validators) {
	}

	public function validate () {
		return true;
	}

	public function isSecure () {
		return false;
	}

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClassName () {
		if (!$this->recordClassName) {
			$prefixes = BSModuleProfile::getPrefixes();
			$module = $this->controller->getModuleName();
			$pattern = sprintf('/^(%s)([A-Z][A-Za-z]+)$/', implode('|', $prefixes));
			if (preg_match($pattern, $module, $matches)) {
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
}

/* vim:set tabstop=4 ai: */
?>