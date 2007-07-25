<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage action
 */

/**
 * Actionのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSAction extends Action {
	protected $recordClassName;
	protected $table;

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClassName () {
		if (!$this->recordClassName) {
			if (defined('APP_MODULE_PREFIXES') && APP_MODULE_PREFIXES) {
				$prefixes = BSString::capitalize(explode(',', APP_MODULE_PREFIXES));
			} else {
				$prefixes = array('Admin', 'User');
			}

			$module = $this->context->getModuleName();
			$pattern = sprintf('/^(%s)([A-Z][a-z]+)$/', implode('|', $prefixes));
			if (preg_match($pattern, $module, $matches)) {
				$this->recordClassName = BSString::capitalize($matches[2]);
			}
		}
		return $this->recordClassName;
	}

	/**
	 * テーブルを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブル
	 */
	protected function getTable () {
		if (!$this->table && $this->getRecordClassName()) {
			$name = $this->getRecordClassName() . 'Handler';
			$this->table = new $name();
		}
		return $this->table;
	}

	public function __get ($name) {
		switch ($name) {
			case 'controller':
				return $this->getContext()->getController();
			case 'request':
				return $this->getContext()->getRequest();
			case 'user':
				return $this->getContext()->getUser();
			case 'context':
				return $this->getContext();
			case 'database':
				return BSDatabase::getInstance();
		}
	}

	public function getDefaultView () {
		return View::SUCCESS;
	}
}

/* vim:set tabstop=4 ai: */
?>