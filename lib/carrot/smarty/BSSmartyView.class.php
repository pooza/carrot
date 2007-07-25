<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * SmartyViewの代用品
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSSmartyView.class.php 339 2007-06-12 13:46:38Z pooza $
 * @link http://ozaki.kyoichi.jp/mojavi3/smarty.html 参考
 * @abstract
 */
abstract class BSSmartyView extends BSView {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 */
	public function initialize ($context) {
		parent::initialize($context);

		// Smartyを設定
		$this->setEngine(new BSSmarty());
		$this->setDirectory($this->context->getModuleDirectory() . '/templates');
		$this->getEngine()->setUserAgent($this->controller->getUserAgent());
		$this->getEngine()->addModifier('sanitize');
		$this->getEngine()->addOutputFilter('trim');

		// ケータイの場合はエンコーディングを変更
		if ($this->useragent->isMobile()) {
			$this->getEngine()->setEncoding('sjis');
			$this->getEngine()->addOutputFilter('mobile');
		}

		// ヘッダを設定
		$this->setHeader('Content-Script-Type', 'text/javascript');
		$this->setHeader('Content-Style-Type', 'text/css');

		// デフォルトで、アクションと同名のテンプレートを使用
		$action = $this->context->getActionName();
		if ($this->getEngine()->getTemplatesDirectory()->getEntry($action)) {
			$this->setTemplate($action);
		}

		// 各種属性をアサイン
		$this->setAttribute('module', $this->context->getModuleName());
		$this->setAttribute('action', $this->context->getActionName());
		$this->setAttribute('errors', $this->request->getErrors());
		$this->setAttribute('params', $this->request->getParameters());
		$this->setAttribute('useragent', $this->useragent->getAttributes());
		$this->setAttribute('menu', $this->request->getAttribute('menu'));
		$this->setAttribute('title', $this->request->getAttribute('title'));
		$this->setAttribute('is_debug', $this->controller->isDebugMode());
		$this->setAttribute('show_error_code', true);

		return true;
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性
	 */
	public function & getAttribute ($name) {
		return $this->getEngine()->getAttribute($name);
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		return $this->getEngine()->getAttributes();
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 属性値
	 */
	public function setAttribute ($name, $value) {
		$this->getEngine()->setAttribute($name, $value);
	}

	/**
	 * ディレクトリを設定
	 *
	 * @access public
	 * @param string $directory ディレクトリ名
	 */
	public function setDirectory ($directory) {
		$dir = new BSDirectory($directory);
		$dir->setSuffix('.tpl');
		$this->getEngine()->setTemplatesDirectory($dir);
		$this->directory = $dir->getPath();
	}

	/**
	 * テンプレートを設定
	 *
	 * @access public
	 * @param string $template テンプレートファイル名
	 */
	public function setTemplate ($template) {
		$this->getEngine()->setTemplate($template);
		parent::setTemplate($template);
	}

	/**
	 * 配列をカラム数で分割する
	 *
	 * @access public
	 * @param mixed[] $array 対象配列
	 * @param integer $columns カラム数
	 * @return mixed[] 分割後の配列
	 * @static
	 */
	public static function columnize ($array, $columns = 3) {
		$array = array_chunk($array, $columns);
		$last = array_pop($array);

		for ($i = count($last) ; $i < $columns ; $i ++) {
			$last[] = null;
		}
		$array[] = $last;

		return $array;
	}
}

/* vim:set tabstop=4 ai: */
?>