<?php
/**
 * JapaneseHolidayListアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class JapaneseHolidayListAction extends BSAction {
	public function execute () {
		// キャッシュを更新
		$this->controller->removeAttribute('BSJapaneseHolidayList');
		BSJapaneseHolidayList::getInstance();

		BSLog::put(get_class($this) . 'を実行しました。');
		return BSView::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>