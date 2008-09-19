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

		$this->setEngine(new BSSmarty);
		if ($dir = $this->controller->getModule()->getDirectory('templates')) {
			$this->renderer->setTemplatesDirectory($dir);
		}
		$this->renderer->setUserAgent($this->request->getUserAgent());
		$this->renderer->addModifier('sanitize');

		if (!$this->request->isCLI()) {
			$this->renderer->addOutputFilter('trim');
		}

		if ($this->useragent->isMobile()) {
			$this->renderer->setEncoding('sjis');
			$this->renderer->addOutputFilter('mobile');
		}

		$this->setHeader('Content-Script-Type', 'text/javascript');
		$this->setHeader('Content-Style-Type', 'text/css');

		$name = $this->controller->getAction()->getName();
		if ($this->renderer->getTemplatesDirectory()->getEntry($name)) {
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
}

/* vim:set tabstop=4 ai: */
?>