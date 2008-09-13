<?php
/**
 * @package org.carrot-framework
 * @subpackage smarty
 */

/**
 * Smarty用のView
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @link http://ozaki.kyoichi.jp/mojavi3/smarty.html 参考
 * @abstract
 */
abstract class BSSmartyView extends BSView {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param boolean 初期化が成功すればTrue
	 */
	public function initialize () {
		parent::initialize();

		$this->setEngine(new BSSmarty());
		if ($dir = $this->controller->getModule()->getDirectory('templates')) {
			$this->getEngine()->setTemplatesDirectory($dir);
		}
		$this->getEngine()->setUserAgent($this->request->getUserAgent());
		$this->getEngine()->addModifier('sanitize');

		if (!$this->request->isCLI()) {
			$this->getEngine()->addOutputFilter('trim');
		}

		if ($this->useragent->isMobile()) {
			$this->getEngine()->setEncoding('sjis');
			$this->getEngine()->addOutputFilter('mobile');
		}

		$this->setHeader('Content-Script-Type', 'text/javascript');
		$this->setHeader('Content-Style-Type', 'text/css');

		$name = $this->controller->getAction()->getName();
		if ($this->getEngine()->getTemplatesDirectory()->getEntry($name)) {
			$this->setTemplate($name);
		}

		$this->setAttributes($this->request->getAttributes());
		$this->setAttribute('module', $this->controller->getModule()->getName());
		$this->setAttribute('action', $this->controller->getAction()->getName());
		$this->setAttribute('errors', $this->request->getErrors());
		$this->setAttribute('params', $this->request->getParameters());
		$this->setAttribute('credentials', $this->user->getCredentials());
		$this->setAttribute('useragent', $this->renderer->getUserAgent()->getAttributes());
		$this->setAttribute('is_debug', $this->controller->isDebugMode());
		$this->setAttribute('is_ssl', $this->request->isSSL());

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
	 * 属性をまとめて設定
	 *
	 * @access public
	 * @param mixed[] $attribures 属性値
	 */
	public function setAttributes ($attributes) {
		foreach ($attributes as $key => $value) {
			$this->getEngine()->setAttribute($key, $value);
		}
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
	 * テンプレートを設定
	 *
	 * @access public
	 * @param string $template テンプレートファイル名
	 */
	public function setTemplate ($template) {
		$this->getEngine()->setTemplate($template);
	}

	/**
	 * 配列をカラム数で分割
	 *
	 * @access public
	 * @param mixed[] $array 対象配列
	 * @param integer $columns カラム数
	 * @return mixed[] 分割後の配列
	 * @static
	 */
	static public function columnize ($array, $columns = 3) {
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