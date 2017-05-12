<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.validate.validator
 */

/**
 * スタイルセット選択バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSStyleSetValidator extends BSChoiceValidator {
	protected function getChoices () {
		$styleset = new BSStyleSet;
		return $styleset->getEntryNames();
	}
}

