<?php
/**
 * @package org.carrot-framework
 * @subpackage view
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

		$this->setRenderer(new BSSmarty);
		$this->renderer->addModifier('sanitize');
		$this->renderer->setUserAgent($this->useragent);
		$this->setHeader('Content-Script-Type', 'text/javascript');
		$this->setHeader('Content-Style-Type', 'text/css');
		$this->setAttributes($this->request->getAttributes());
		$this->setAttribute('module', $this->controller->getModule());
		$this->setAttribute('action', $this->controller->getAction());
		$this->setAttribute('errors', $this->request->getErrors());
		$this->setAttribute('params', $this->request->getParameters());
		$this->setAttribute('credentials', $this->user->getCredentials());
		$this->setAttribute('is_debug', BS_DEBUG);
		$this->setAttribute('is_ssl', $this->request->isSSL());

		if ($dir = $this->controller->getModule()->getDirectory('templates')) {
			$this->renderer->setTemplatesDirectory($dir);
		}
		if ($file = $this->getDefaultTemplateFile()) {
			$this->setTemplate($file);
		}

		return true;
	}

	/**
	 * 規定のテンプレートを返す
	 *
	 * @access protected
	 * @param BSFile テンプレートファイル
	 */
	protected function getDefaultTemplateFile () {
		$names = array(
			$this->getAction()->getName() . '.' . $this->getNameSuffix(),
			$this->getAction()->getName(),
		);
		foreach ($names as $name) {
			if ($file = $this->renderer->getTemplateFile($name)) {
				return $file;
			}
		}
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

/* vim:set tabstop=4: */
