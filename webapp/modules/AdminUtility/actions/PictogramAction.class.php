<?php
/**
 * Pictogramアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminUtility
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class PictogramAction extends BSAction {

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return '絵文字入力';
	}

	public function execute () {
		$this->request->setAttribute('pictograms', BSPictogram::getPictogramImageInfos());
		return BSView::INPUT;
	}
}

/* vim:set tabstop=4: */
