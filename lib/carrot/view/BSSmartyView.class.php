<?php
/**
 * @package org.carrot-framework
 * @subpackage view
 */

/**
 * Smartyレンダラー用の基底ビュー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @link http://ozaki.kyoichi.jp/mojavi3/smarty.html 参考
 */
class BSSmartyView extends BSView {

	/**
	 * @access public
	 * @param BSAction $action 呼び出し元アクション
	 * @param string $suffix ビュー名サフィックス
	 * @param BSRenderer $renderer レンダラー
	 */
	public function __construct (BSAction $action, $suffix, BSRenderer $renderer = null) {
		$this->action = $action;
		$this->nameSuffix = $suffix;

		if ($renderer) {
			if (($renderer instanceof BSSmarty) == false) {
				throw new BSViewException(
					'BSSmartyViewに%sをセット出来ません。',
					get_class($renderer)
				);
			}
		} else {
			$renderer = new BSSmarty;
		}
		$this->setRenderer($renderer);
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param boolean 初期化が成功すればTrue
	 */
	public function initialize () {
		parent::initialize();
		$this->renderer->addModifier('sanitize');
		$this->renderer->setUserAgent($this->useragent);
		$this->setHeader('Content-Script-Type', BSMIMEType::getType('js'));
		$this->setHeader('Content-Style-Type', BSMIMEType::getType('css'));
		$this->setAttributes($this->request->getAttributes());
		$this->setAttribute('module', $this->controller->getModule());
		$this->setAttribute('action', $this->controller->getAction());
		$this->setAttribute('errors', $this->request->getErrors());
		$this->setAttribute('params', $this->request->getParameters());
		$this->setAttribute('credentials', $this->user->getCredentials());
		$this->setAttribute('client_host', $this->request->getHost());
		$this->setAttribute('server_host', $this->controller->getHost());
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
	 * @access public
	 * @param BSTemplateFile テンプレートファイル
	 */
	public function getDefaultTemplateFile () {
		$names = array(
			$this->getAction()->getName() . '.' . $this->getNameSuffix(),
			$this->getAction()->getName(),
		);
		foreach ($names as $name) {
			if ($file = $this->renderer->searchTemplate($name)) {
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
		if ($array instanceof BSParameterHolder) {
			$array = $array->getParameters();
		}

		$array = new BSArray(array_chunk($array, $columns));
		$last = new BSArray($array->pop());

		for ($i = $last->count() ; $i < $columns ; $i ++) {
			$last[] = null;
		}
		$array[] = $last;

		return $array;
	}
}

/* vim:set tabstop=4: */
