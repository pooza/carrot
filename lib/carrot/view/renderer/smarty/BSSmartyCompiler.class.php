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

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性
	 */
	public function getAttribute ($name) {
		return $this->$name;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 属性値
	 */
	public function setAttribute ($name, $value) {
		$this->$name = $value;
	}

	/**
	 * テンプレートファイルを返す
	 *
	 * @access public
	 * @return BSTemplateFile テンプレートファイル
	 */
	public function getTemplate () {
		return $this->template;
	}

	/**
	 * Compile {foreach ...} tag.
	 *
	 * @access public
	 * @param string $args
	 * @return string
	 */
	public function _compile_foreach_start ($args) {
		$params = new BSArray($this->_parse_attrs($args));
		if (BSString::isBlank($params['name'])) {
			$params['name'] = BSUtility::getUniqueID();
		}
		if (BSString::isBlank($this->_dequote($params['item']))) {
			$params['item'] = 'item' . BSUtility::getUniqueID();
		}
		if (BSString::isBlank($this->_dequote($params['key']))) {
			$params['key'] = 'key' . BSUtility::getUniqueID();
		}
		$var = '$this->_foreach[' . $this->quote($params['name']) . ']';

		$body = new BSArray;
		$body[] = sprintf(
			'<?php %s = array(%s => %s, %s => 0);',
			$var,
			$this->quote('from'),
			$params['from'],
			$this->quote('iteration')
		);
		$body[] = sprintf('%s[%s] = count(%s[%s]);',
			$var,
			$this->quote('total'),
			$var,
			$this->quote('from')
		);

		$body[] = sprintf('if (0 < %s[%s]):', $var, $this->quote('from'));
		$body[] = sprintf(
			'  foreach (%s[%s] as $this->_tpl_vars[%s] => $this->_tpl_vars[%s]):',
			$var,
			$this->quote('from'),
			$this->quote($params['key']),
			$this->quote($params['item'])
		);
		$body[] = sprintf('    %s[%s] ++;', $var, $this->quote('iteration'));

		if ($limit = $params['limit']) {
			$body[] = sprintf(
				'    if (%d < %s[%s]) {break;}',
				$limit, $var, $this->quote('iteration')
			);
		}

		$body[] = '?>';
		return $body->join("\n");
	}

	private function quote ($value) {
		if (is_string($value)) {
			$value = $this->_dequote($value);
		}
		$value = BSConfigCompiler::quote($value);
		return $value;
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
