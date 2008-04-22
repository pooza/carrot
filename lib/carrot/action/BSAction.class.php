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
 * @version $Id: BSAction.class.php 208 2008-04-20 01:52:19Z pooza $
 * @abstract
 */
abstract class BSAction extends Action {
	protected $recordClassName;

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClassName () {
		if (!$this->recordClassName) {
			$prefixes = BSModuleProfile::getPrefixes();
			$module = $this->context->getModuleName();
			$pattern = sprintf('/^(%s)([A-Z][A-Za-z]+)$/', implode('|', $prefixes));
			if (preg_match($pattern, $module, $matches)) {
				$this->recordClassName = $matches[2];
			}
		}
		return $this->recordClassName;
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
		return BSView::SUCCESS;
	}
}

/* vim:set tabstop=4 ai: */
?>