<?php
/**
 * Pictogramアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminUtility
 * @author 小石達也 <tkoishi@b-shock.co.jp>

 */
class PictogramAction extends BSAction {
	public function execute () {
		$this->request->setAttribute('pictograms', BSPictogram::getPictogramImageInfos());
		return BSView::INPUT;
	}
}

/* vim:set tabstop=4: */
