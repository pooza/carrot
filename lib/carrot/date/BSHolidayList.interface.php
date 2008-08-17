<?php
/**
 * @package org.carrot-framework
 * @subpackage date
 */

/**
 * 祝日リスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
interface BSHolidayList {

	/**
	 * コンストラクタ
	 *
	 * 対象日付の年月のみ参照され、日は捨てられる。
	 *
	 * @access public
	 * @param BSDate $date 対象日付
	 */
	public function __construct (BSDate $date = null);

	/**
	 * 祝日を返す
	 *
	 * @access public
	 * @return BSArray 祝日配列
	 */
	public function getHolidays ();
}

/* vim:set tabstop=4 ai: */
?>