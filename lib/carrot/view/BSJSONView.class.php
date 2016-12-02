<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view
 */

/**
 * API結果文書用 既定ビュー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJSONView extends BSView {

	/**
	 * @access public
	 * @param BSAction $action 呼び出し元アクション
	 * @param string $suffix ビュー名サフィックス
	 * @param BSRenderer $renderer レンダラー
	 */
	public function __construct (BSAction $action, $suffix, BSRenderer $renderer = null) {
		parent::__construct($action, $suffix, $renderer);

		if ($status = $this->request->getAttribute('status')) {
			$this->setStatus($status);
		} else {
			if ($suffix == self::ERROR) {
				$this->setStatus(400);
			} else {
				$this->setStatus(200);
			}
		}
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
		if (!($renderer instanceof BSResultJSONRenderer)) {
			$dest = new BSResultJSONRenderer;
			if ($renderer instanceof BSJSONRenderer) {
				$dest->setContents(new BSArray($renderer->getResult()));
			}
			$renderer = $dest;
		}
		if (!($renderer instanceof BSJSONRenderer)) {
			throw new BSViewException(get_class($renderer) . 'をセットできません。');
		}
		parent::setRenderer($renderer, $flags);
	}

	/**
	 * レンダリング
	 *
	 * @access public
	 */
	public function render () {
		$params = $this->renderer->getParameters();
		$params['status'] = $this->getStatus();
		$params['module'] = $this->getModule()->getName();
		$params['action'] = $this->getAction()->getName();
		$params['params'] = $this->request->getParameters();
		if ($this->request->hasErrors()) {
			$params['errors'] = $this->request->getErrors();
		}
		parent::render();
	}

	/**
	 * HTTPキャッシュ有効か
	 *
	 * @access public
	 * @return boolean 有効ならTrue
	 */
	public function isCacheable () {
		return false;
	}
}

/* vim:set tabstop=4: */
