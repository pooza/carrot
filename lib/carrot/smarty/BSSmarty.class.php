<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

BSController::includeFile('Smarty/Smarty.class.php');

/**
 * Smartyのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSmarty extends Smarty implements BSTextRenderer {
	private $type;
	private $encoding;
	private $template;
	private $templatesDirectory;
	private $error;
	private $useragent;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct() {
		$this->config_dir = BSController::getInstance()->getPath('config');
		$this->cache_dir = BSController::getInstance()->getPath('cache');
		$this->compile_dir = BSController::getInstance()->getPath('compile');
		$this->plugins_dir[] = BSController::getInstance()->getPath('carrot') . '/smarty';
		$this->plugins_dir[] = BSController::getInstance()->getPath('local_lib') . '/smarty';
		$this->force_compile = BSController::getInstance()->isDebugMode();
		$this->addModifier('encoding');
		$this->setEncoding(BSString::TEMPLATE_ENCODING);
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
		return $this->fetch($this->getTemplate());
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
	 * 対象UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent 対象UserAgent
	 */
	public function getUserAgent () {
		return $this->useragent;
	}

	/**
	 * 対象UserAgentを設定する
	 *
	 * @access public
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function setUserAgent (BSUserAgent $useragent) {
		$this->useragent = $useragent;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		if (!$this->type) {
			$this->type = BSMediaType::getType('html');
		}
		return $this->type;
	}

	/**
	 * メディアタイプを設定する
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
	 * エンコードを設定する
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
	 * テンプレートを返す
	 *
	 * @access public
	 * @return string テンプレートファイル名
	 */
	public function getTemplate () {
		return $this->template;
	}

	/**
	 * テンプレートを設定
	 *
	 * @access public
	 * @param string $template テンプレートファイル名
	 */
	public function setTemplate ($template) {
		if (!$file = $this->getTemplateFile($template)) {
			throw new BSSmartyException('"%s"が見つかりません。', $template);
		}
		$this->template = $file->getPath();
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
		if ($value instanceof BSArray) {
			$this->assign($name, $value->getParameters());
		} else if ($value != '') {
			$this->assign($name, $value);
		}
	}

	/**
	 * ファイル名から実テンプレートファイルを返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return BSFile 実テンプレートファイル
	 */
	public function getTemplateFile ($name) {
		if (BSUtility::isPathAbsolute($name)) {
			$file = new BSFile($name);
			if ($file->isReadable()) {
				return $file;
			}
		} else {
			$name = preg_replace('/\.tpl$/i', '', $name);
			$dirs = array(
				$this->getTemplatesDirectory(),
				BSController::getInstance()->getDirectory('templates'),
			);
			$names = array($name);
			if ($this->getUserAgent()) {
				if ($this->getUserAgent()->isMobile()) {
					$names[] = $name . '.mobile';
				}
				$names[] = $name . '.' . $this->getUserAgent()->getType();
				rsort($names);
			}
			foreach ($dirs as $dir) {
				foreach ($names as $name) {
					if ($file = $dir->getEntry($name)) {
						return $file;
					}
				}
			}
		}
	}

	/**
	 * includeタグの拡張
	 *
	 * @access public
	 * @param mixed[] $params パラメータ一式
	 */
	public function _smarty_include ($params) {
		$template =& $params['smarty_include_tpl_file'];
		if ($file = $this->getTemplateFile($template)) {
			$template = $file->getPath();
			return parent::_smarty_include($params);
		}
		throw new BSSmartyException('"%s"が見つかりません。', $template);
	}

	/**
	 * エラートリガ
	 *
	 * @access public
	 * @param string $error_msg エラーメッセージ
	 * @param integer $error_type
	 */
	public function trigger_error ($error_msg, $error_type = null) {
		throw new BSSmartyException($error_msg);
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
		// ソーステンプレート名をフルパス表記に修正する
		if (!BSUtility::isPathAbsolute($source)) {
			$source = preg_replace('/\/*$/', '/', $base) . $source;
		}
		return parent::_get_auto_filename($base, $source, $id);
	}
}

/* vim:set tabstop=4 ai: */
?>