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
		$pictograms = new BSArray;
		foreach (BSPictogram::getPictograms() as $name => $pictogram) {
			$values = new BSArray(array(
				'name' => $name,
				'id' => $pictogram->getID(),
				'image' => $pictogram->getImageInfo(),
			));
			$pictograms[$name] = $values;
		}
		$this->request->setAttribute('pictograms', $pictograms);
		return BSView::INPUT;
	}
}

/* vim:set tabstop=4: */
