<?php
/**
 * Choiceアクション
 *
 * @package org.carrot-framework
 * @subpackage UserPictogram
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class ChoiceAction extends BSAction {
	public function execute () {
		$this->request->setAttribute('pictograms', BSPictogram::getPictogramNames());
		return BSView::INPUT;
	}
}

/* vim:set tabstop=4: */
