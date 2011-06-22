<?php
/**
 * @package org.carrot-framework
 * @subpackage view
 */

/**
 * Smartyレンダラー用の基底ビュー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSmartyView extends BSView {

	/**
	 * @access public
	 * @param BSAction $action 呼び出し元アクション
	 * @param string $suffix ビュー名サフィックス
	 * @param BSRenderer $renderer レンダラー
	 */
	public function __construct (BSAction $action, $suffix, BSRenderer $renderer = null) {
		parent::__construct($action, $suffix, $renderer);

		$this->setHeader('Content-Script-Type', BSMIMEType::getType('js'));
		$this->setHeader('Content-Style-Type', BSMIMEType::getType('css'));
	}

	/**
	 * 規定のレンダラーを生成して返す
	 *
	 * @access protected
	 * @return BSRenderer レンダラー
	 */
	protected function createDefaultRenderer () {
		return new BSSmarty;
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
		if (BS_VIEW_MOBILE_XHTML && $this->request->getRealUserAgent()->isMobile()) {
			$this->renderer->setType(BSMIMEType::getType('xhtml'));
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
}

/* vim:set tabstop=4: */
