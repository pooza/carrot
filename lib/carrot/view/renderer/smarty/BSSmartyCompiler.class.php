<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty
 */

BSUtility::includeFile('Smarty/Smarty_Compiler.class.php');

/**
 * Smarty_Compilerラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSmartyCompiler extends Smarty_Compiler {

	/**
	 * 初期化
	 *
	 * 生成元Smartyオブジェクトのプロパティをコピー
	 *
	 * @access public
	 * @param BSSmarty $smarty 生成元Smartyオブジェクト
	 * @return boolean 成功したらTrue
	 */
	public function initialize (BSSmarty $smarty) {
		$this->template_dir = $smarty->template_dir;
		$this->compile_dir = $smarty->compile_dir;
		$this->plugins_dir = $smarty->plugins_dir;
		$this->config_dir = $smarty->config_dir;
		$this->force_compile = $smarty->force_compile;
		$this->caching = $smarty->caching;
		$this->php_handling = $smarty->php_handling;
		$this->left_delimiter = $smarty->left_delimiter;
		$this->right_delimiter = $smarty->right_delimiter;
		$this->_version = $smarty->_version;
		$this->security = $smarty->security;
		$this->secure_dir = $smarty->secure_dir;
		$this->security_settings = $smarty->security_settings;
		$this->trusted_dir = $smarty->trusted_dir;
		$this->use_sub_dirs = $smarty->use_sub_dirs;
		$this->_reg_objects = &$smarty->_reg_objects;
		$this->_plugins = &$smarty->_plugins;
		$this->_tpl_vars = &$smarty->_tpl_vars;
		$this->default_modifiers = $smarty->default_modifiers;
		$this->compile_id = $smarty->_compile_id;
		$this->_config = $smarty->_config;
		$this->request_use_auto_globals = $smarty->request_use_auto_globals;
		$this->template = $smarty->getTemplate();
		return true;
	}

	public function getAttribute ($name) {
		return $this->$name;
	}

	public function setAttribute ($name, $value) {
		$this->$name = $value;
	}

	/**
	 * テンプレートを返す
	 *
	 * @access public
	 * @return string テンプレートファイル名
	 */
	public function getTemplate () {
		return $this->template;
	}

	/**
	 * エラートリガ
	 *
	 * @access public
	 * @param string $error_msg エラーメッセージ
	 * @param integer $error_type
	 */
	public function trigger_error ($error_msg, $error_type = null) {
		throw new BSViewException($error_msg);
	}
}

/* vim:set tabstop=4: */
