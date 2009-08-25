<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty
 */

BSUtility::includeFile('Smarty/Smarty.class.php');

/**
 * Smartyラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSmarty extends Smarty implements BSTextRenderer {
	private $type;
	private $encoding;
	private $template;
	private $templatesDirectory;
	private $error;
	private $useragent;
	private $headers;
	private $storage;
	private $compiler;
	public $compiler_class = 'BSSmartyCompiler';

	/**
	 * @access public
	 */
	public function __construct() {
		$controller = BSController::getInstance();
		$this->compile_dir = $controller->getPath('compile');
		$this->plugins_dir = array();
		$this->plugins_dir[] = $controller->getPath('local_lib') . '/smarty';
		$this->plugins_dir[] = $controller->getPath('carrot') . '/view/renderer/smarty/plugins';
		$this->plugins_dir[] = $controller->getPath('lib') . '/Smarty/plugins';
		$this->force_compile = BS_DEBUG;
		$this->addModifier('encoding');
		$this->setEncoding('utf-8');

		$storage = BSClassLoader::getInstance()->getObject(
			BS_SMARTY_CACHE_STORAGE,
			'SmartyCacheStorage'
		);
		if (!$storage->initialize($this)) {
			$storage = new BSDefaultSmartyCacheStorage;
			$storage->initialize($this);
		}
		$this->storage = $storage;
	}

	/**
	 * テンプレートディレクトリを返す
	 *
	 * @access public
	 * @return BSDirectory テンプレートディレクトリ
	 */
	public function getTemplatesDirectory () {
		if (!$this->templatesDirectory) {
			$dir = BSController::getInstance()->getDirectory('templates');
			$this->setTemplatesDirectory($dir);
		}
		return $this->templatesDirectory;
	}

	/**
	 * テンプレートディレクトリを設定
	 *
	 * @access public
	 * @param BSDirectory $dir テンプレートディレクトリ
	 */
	public function setTemplatesDirectory (BSDirectory $dir) {
		$dir->setDefaultSuffix('.tpl');
		$this->template_dir = $dir->getPath();
		$this->templatesDirectory = $dir;
	}

	/**
	 * 規定の修飾子を追加
	 *
	 * @access public
	 * @param string $name 修飾子の名前
	 */
	public function addModifier ($name) {
		$this->default_modifiers[] = $name;
	}

	/**
	 * 規定の修飾子をクリア
	 *
	 * @access public
	 * @param string $name 修飾子の名前
	 */
	public function clearModifier () {
		$this->default_modifiers = array();
	}

	/**
	 * プレフィルタを追加
	 *
	 * @access public
	 * @param string $name プレフィルタの名前
	 */
	public function addPreFilter ($name) {
		$this->load_filter('pre', $name);
	}

	/**
	 * ポストフィルタを追加
	 *
	 * @access public
	 * @param string $name ポストフィルタの名前
	 */
	public function addPostFilter ($name) {
		$this->load_filter('post', $name);
	}

	/**
	 * 出力フィルタを追加
	 *
	 * @access public
	 * @param string $name 出力フィルタの名前
	 */
	public function addOutputFilter ($name) {
		$this->load_filter('output', $name);
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		return $this->getTemplate()->compile();
	}

	/**
	 * 送信内容を返す
	 *
	 * getContentsのエイリアス
	 *
	 * @access public
	 * @return string 送信内容
	 * @final
	 */
	final public function render () {
		return $this->getContents();
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * ヘッダ一式を返す
	 *
	 * @access public
	 * @return string[] ヘッダ一式
	 */
	public function getHeaders () {
		if (!$this->headers) {
			$this->headers = new BSArray;
		}
		return $this->headers;
	}

	/**
	 * 対象UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent 対象UserAgent
	 */
	public function getUserAgent () {
		return $this->useragent;
	}

	/**
	 * 対象UserAgentを設定
	 *
	 * @access public
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function setUserAgent (BSUserAgent $useragent) {
		$this->useragent = $useragent;
		$this->setAttribute('useragent', $useragent);
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		if (!$this->type) {
			$this->type = BSMIMEType::getType('html');
		}
		return $this->type;
	}

	/**
	 * メディアタイプを設定
	 *
	 * @access public
	 * @param string $type メディアタイプ
	 */
	public function setType ($type) {
		$this->type = $type;
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return $this->encoding;
	}

	/**
	 * エンコードを設定
	 *
	 * @access public
	 * @param string $encoding エンコード
	 */
	public function setEncoding ($encoding) {
		$this->encoding = $encoding;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!$this->getTemplate()) {
			$this->error = 'テンプレートが未定義です。';
			return false;
		}
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
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
	 * テンプレートを設定
	 *
	 * @access public
	 * @param mixied $template テンプレートファイル名、又はテンプレートファイル
	 */
	public function setTemplate ($template) {
		if (!$file = $this->searchTemplate($template)) {
			throw new BSViewException('テンプレート"%s"が見つかりません。', $template);
		}
		$this->template = $file;
		$this->template->setEngine($this);
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性
	 */
	public function getAttribute ($name) {
		return $this->get_template_vars($name);
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		return $this->get_template_vars();
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 属性値
	 */
	public function setAttribute ($name, $value) {
		if (is_array($name) || is_object($name)) {
			throw new BSRegisterException('属性名が文字列ではありません。');
		}
		if ($value instanceof BSAssignable) {
			$this->assign($name, $value->getAssignValue());
		} else if (!BSString::isBlank($value)) {
			$this->assign($name, $value);
		}
	}

	/**
	 * 属性をまとめて設定
	 *
	 * @access public
	 * @param mixed[] $attribures 属性値
	 */
	public function setAttributes ($attributes) {
		foreach ($attributes as $key => $value) {
			$this->setAttribute($key, $value);
		}
	}

	/**
	 * ファイル名から実テンプレートファイルを返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return BSTemplateFile 実テンプレートファイル
	 */
	public function searchTemplate ($name) {
		if ($name instanceof BSFile) {
			return new BSTemplateFile($name->getPath());
		} else if (BSUtility::isPathAbsolute($name)) {
			$file = new BSTemplateFile($name);
			if ($file->isReadable()) {
				return $file;
			}
		} else {
			$name = preg_replace('/\.tpl$/i', '', $name);
			$directories = new BSArray;
			$directories[] = $this->getTemplatesDirectory();
			$directories[] = BSController::getInstance()->getDirectory('templates');
			$names = new BSArray($name);
			if ($this->getUserAgent()) {
				if ($this->getUserAgent()->isMobile()) {
					$names[] = $name . '.mobile';
				}
				$names[] = $name . '.' . $this->getUserAgent()->getType();
				$names->sort(BSArray::SORT_KEY_DESC);
			}
			foreach ($directories as $directory) {
				foreach ($names as $name) {
					if ($file = $directory->getEntry($name, 'BSTemplateFile')) {
						return $file;
					}
				}
			}
		}
	}

	/**
	 * コンパイラを返す
	 *
	 * @return BSSmartyCompiler コンパイラ
	 */
	public function getCompiler () {
		if (!$this->compiler) {
			$this->compiler = new $this->compiler_class;
			if (!$this->compiler->initialize($this)) {
				throw new BSInitializationException(
					'%sが初期化出来ません。',
					$this->compiler_class
				);
			}
		}
		return $this->compiler;
	}

	/**
	 * リソースを指定してコンパイル
	 *
	 * @param string $resource リソース名
	 * @param string $source ソース文字列
	 * @param string $compiled コンパイル済み文字列
	 * @param string $path コンパイル済みテンプレートへのパス
	 * @return boolean 成功ならTrue
	 */
	public function _compile_source ($resource, &$source, &$compiled, $path = null) {
		$compiler = $this->getCompiler();

		if (isset($path) && isset($this->_cache_serials[$path])) {
			$compiler->setAttribute('_cache_serial', $this->_cache_serials[$path]);
		}
		$compiler->setAttribute('_cache_include', $path);
		$result = $compiler->_compile_file($resource, $source, $compiled);

		if ($compiler->_cache_serial) {
			$this->_cache_include_info = array(
				'cache_serial' => $compiler->getAttribute('_cache_serial'),
				'plugins_code' => $compiler->getAttribute('_plugins_code'),
				'include_file_path' => $path,
			);
		} else {
			$this->_cache_include_info = null;
		}
		return $result;
	}

	/**
	 * includeタグの拡張
	 *
	 * @access public
	 * @param mixed[] $params パラメータ一式
	 */
	public function _smarty_include ($params) {
		$template =& $params['smarty_include_tpl_file'];
		if ($file = $this->searchTemplate($template)) {
			$template = $file->getPath();
			return parent::_smarty_include($params);
		}
		throw new BSViewException('テンプレート"%s"が見つかりません。', $template);
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

	/**
	 * コンパイル先ファイル名を返す
	 *
	 * @access public
	 * @param string $base コンパイルディレクトリ
	 * @param string $source ソーステンプレート名
	 * @param string $id
	 * @return string
	 */
	public function _get_auto_filename ($base, $source = null, $id = null) {
		// ソーステンプレート名をフルパス表記に修正
		if (!BSUtility::isPathAbsolute($source)) {
			$pattern = '/' . preg_quote(DIRECTORY_SEPARATOR, '/') . '*$/';
			$source = preg_replace($pattern, DIRECTORY_SEPARATOR, $base) . $source;
		}
		return parent::_get_auto_filename($base, $source, $id);
	}
}

/* vim:set tabstop=4: */
