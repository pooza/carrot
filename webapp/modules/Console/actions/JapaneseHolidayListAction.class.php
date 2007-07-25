<?php
/**
 * JapaneseHolidayListアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: JapaneseHolidayListAction.class.php 160 2006-07-16 15:42:12Z pooza $
 */
class JapaneseHolidayListAction extends BSAction {
	public function execute () {
		// キャッシュを更新
		$this->controller->removeAttribute('BSJapaneseHolidayList');
		BSJapaneseHolidayList::getInstance();
		return View::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>