<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view
 */

/**
 * レンダラーを持たないビュー
 *
 * アクションがBSView::NONEを返したとき、HEADリクエストされたとき等に使用。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSEmptyView extends BSView {

	/**
	 * 初期化
	 *
	 * @access public
	 * @return boolean 初期化が成功すればTrue
	 */
	public function initialize () {
		return true;
	}

	/**
	 * レンダリング
	 *
	 * ヘッダの送信のみ。
	 *
	 * @access public
	 */
	public function render () {
		$this->putHeaders();
	}
}

