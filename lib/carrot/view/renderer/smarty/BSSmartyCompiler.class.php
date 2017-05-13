<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty
 */

BSUtility::includeFile('Smarty/Smarty_Compiler.class');

/**
 * Smarty_Compilerラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
			$params['name'] = 'foreach_' . BSUtility::getUniqueID();
		}
		if (BSString::isBlank($params['item'])) {
			$params['item'] = $params['name'] . '_item';
		}
		if (BSString::isBlank($params['key'])) {
			$params['key'] = $params['name'] . '_key';
		}

		$var = '$this->_foreach[' . $this->quote($params['name']) . ']';
		$body = new BSArray;
		$body[] = sprintf(
			'<?php %s = [\'from\' => %s, \'iteration\' => 0];',
			$var, $params['from']
		);
		$body[] = sprintf('%s[\'total\'] = count(%s[\'from\']);', $var, $var);
		$body[] = sprintf('if (0 < %s[\'total\']):', $var);
		$body[] = sprintf(
			'  foreach (%s[\'from\'] as $this->_tpl_vars[%s] => $this->_tpl_vars[%s]):',
			$var, $this->quote($params['key']), $this->quote($params['item'])
		);
		$body[] = sprintf('  %s[\'iteration\'] ++;', $var);
		if ($max = $params['max']) {
			$body[] = sprintf('  if (%s < %s[\'iteration\']) {break;}', $max, $var);
		}
		$body[] = '?>';
		return $body->join("\n");
	}

	/**
	 * compile a resource
	 *
	 * sets $contents to the compiled source
	 * @param string $resource
	 * @param string $source
	 * @param string $contents
	 * @return true
	 */
	public function _compile_file ($resource, $source, &$contents) {
		$this->_load_filters();
		$this->_current_file = $resource;
		$source = $this->removeComments($source);
		$blocks = $this->split($source);
		$compiledTags = $this->compileTags($this->fetchTemplateTags($source), $blocks);
		$this->parseStripTag($compiledTags, $blocks);
		$contents = $this->getHeader()->join("\n") . "\n" . $this->join($compiledTags, $blocks);
		return true;
	}

	private function removeComments ($source) {
		$ldq = preg_quote($this->left_delimiter, '~');
		$rdq = preg_quote($this->right_delimiter, '~');
		return preg_replace_callback(
			"~{$ldq}\*.*?\*{$rdq}~s",
			function ($matches) {
				return str_repeat("\n", substr_count($matches[0], "\n"));
			},
			$source
		);
	}

	private function fetchTemplateTags ($source) {
		$ldq = preg_quote($this->left_delimiter, '~');
		$rdq = preg_quote($this->right_delimiter, '~');
		preg_match_all("~{$ldq} *([^\n]+?) *{$rdq}~s", $source, $matches);
		return BSArray::create($matches[1]);
	}

	private function split ($source) {
		$ldq = preg_quote($this->left_delimiter, '~');
		$rdq = preg_quote($this->right_delimiter, '~');
		return BSArray::create(preg_split("~{$ldq}[^\n]+?{$rdq}~s", $source));
	}

	private function compileTags (BSArray $tags, BSArray $blocks) {
		$this->_current_line_no = 1;
		$compiledTags = new BSArray;
		for ($i = 0 ; $i < count($tags) ; $i ++) {
			$this->_current_line_no += substr_count($blocks[$i], "\n");
			$compiledTags[] = $this->_compile_tag($tags[$i]);
			$this->_current_line_no += substr_count($tags[$i], "\n");
		}
		if (0 < count($this->_tag_stack)) {
			list($_open_tag, $_line_no) = end($this->_tag_stack);
			$message = new BSStringFormat('閉じられていないタグ "%s" があります。(%s, %d行目)');
			$message[] = $_open_tag;
			$message[] = (new BSFile($resource))->getShortPath();
			$message[] = $_line_no;
			throw new BSViewException($message, $this);
		}
		return $compiledTags;
	}

	private function parseStripTag (BSArray $compiledTags, BSArray $blocks) {
		$strip = false;
		for ($i = 0 ; $i < $compiledTags->count() ; $i ++) {
			if ($compiledTags[$i] == '{strip}') {
				$compiledTags[$i] = '';
				$strip = true;
				$blocks[$i + 1] = ltrim($blocks[$i + 1]);
			}
			if ($strip) {
				for ($j = $i + 1 ; $j < $compiledTags->count() ; $j ++) {
					$blocks[$j] = preg_replace('![\t ]*[\r\n]+[\t ]*!', '', $blocks[$j]);
					if ($compiledTags[$j] == '{/strip}') {
						$blocks[$j] = rtrim($blocks[$j]);
					}
					$blocks[$j] = "<?php echo '" . strtr($blocks[$j], ["'"=>"\'", "\\"=>"\\\\"]) . "'; ?>";
					if ($compiledTags[$j] == '{/strip}') {
						$compiledTags[$j] = "\n";
						$strip = false;
						$i = $j;
						break;
					}
				}
			}
		}
	}

	private function join (BSArray $compiledTags, BSArray $blocks) {
		$contents = new BSArray;
		$tag_guard = '%%%SMARTYOTG' . BSUtility::getUniqueID() . '%%%';
		for ($i = 0 ; $i < $compiledTags->count() ; $i ++) {
			if ($compiledTags[$i] == '') {
				$blocks[$i + 1] = preg_replace('~^(\r\n|\r|\n)~', '', $blocks[$i + 1]);
			}
			$blocks[$i] = str_replace('<?', $tag_guard, $blocks[$i]);
			$compiledTags[$i] = str_replace('<?', $tag_guard, $compiledTags[$i]);
			$contents[] = $blocks[$i] . $compiledTags[$i];
		}
		$contents[] = str_replace('<?', $tag_guard, $blocks[$i]);
		$contents = $contents->join();
		$contents = str_replace('<?', "<?= '<?' ?>\n", $contents);
		$contents = str_replace($tag_guard, '<?', $contents);
		return trim($contents);
	}

	private function getHeader () {
		$header = new BSArray;
		$header[] = '<?php';
		$header[] = '// auth-generated by ' . __CLASS__;
		$header[] = '// date: ' . BSDate::getNow('%Y/%m/%d %H:%M:%S');
		if (0 < count($this->_plugin_info)) {
			$plugins = ['plugins' => []];
			foreach ($this->_plugin_info as $type => $plugin) {
				foreach ($plugin as $name => $info) {
					$plugins['plugins'][] = [
						$type,
						$name,
						strtr($info[0], ["'" => "\\'", "\\" => "\\\\"]),
						$info[1],
						!!$info[2],
					];
				}
			}
			$header[] = 'require_once(SMARTY_CORE_DIR . \'core.load_plugins.php\');';
			$header[] = 'smarty_core_load_plugins(' . $this->quote($plugins) . ', $this);';
			$this->_plugin_info = [];
		}
		$header[] = '?>';
		return $header;
	}

	private function quote ($value) {
		$value = BSString::dequote($value);
		$value = BSConfigCompiler::quote($value);
		return $value;
	}

	/**
	 * クォートされた文字列から、クォートを外す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 */
	public function _dequote ($value) {
		return BSString::dequote($value);
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

