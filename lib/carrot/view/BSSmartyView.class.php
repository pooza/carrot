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

		if (!$renderer) {
			$renderer = new BSSmarty;
		}
		$this->setRenderer($renderer);

		$this->setHeader('Content-Script-Type', BSMIMEType::getType('js'));
		$this->setHeader('Content-Style-Type', BSMIMEType::getType('css'));
		$this->setHeader('X-Frame-Options', 'deny');
	}

	/**
	 * レンダラーを設定
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 * @param integer $flags フラグのビット列
	 *   BSMIMEUtility::WITHOUT_HEADER ヘッダを修正しない
	 *   BSMIMEUtility::WITH_HEADER ヘッダも修正
	 */
	public function setRenderer (BSRenderer $renderer, $flags = BSMIMEUtility::WITH_HEADER) {
		if (!($renderer instanceof BSSmarty)) {
			throw new BSViewException(get_class($renderer) . 'をセットできません。');
		}

		parent::setRenderer($renderer, $flags);
		if (!$this->useragent->initializeView($this)) {
			throw new BSViewException('ビューを初期化できません。');
		}

		if ($dir = $this->controller->getModule()->getDirectory('templates')) {
			$this->renderer->registerDirectory($dir);
		}
		if ($file = $this->getDefaultTemplate()) {
			$this->renderer->setTemplate($file);
		}
	}

	/**
	 * 規定のテンプレートを返す
	 *
	 * @access protected
	 * @param BSTemplateFile テンプレートファイル
	 */
	protected function getDefaultTemplate () {
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
	 * @param mixed[] $items 対象配列
	 * @param integer $columns カラム数
	 * @return mixed[] 分割後の配列
	 * @static
	 */
	static public function columnize ($items, $columns = 3) {
		if ($items instanceof BSParameterHolder) {
			$items = $items->getParameters();
		}

		$items = new BSArray(array_chunk($items, $columns));
		$last = new BSArray($items->pop());

		for ($i = $last->count() ; $i < $columns ; $i ++) {
			$last[] = null;
		}
		$items[] = $last;

		return $items;
	}
}

/* vim:set tabstop=4: */
