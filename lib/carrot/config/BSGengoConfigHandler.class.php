<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * 元号設定
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSGengoConfigHandler.class.php 205 2008-04-19 11:50:49Z pooza $
 */
class BSGengoConfigHandler extends BSConfigHandler {
	public function execute ($path) {
		$dates = new BSArray;
		foreach ($this->getConfig($path) as $gengo => $params) {
			$line = sprintf(
				'self::$japaneseCalendarDates[%s] = %s;',
				parent::literalize($gengo),
				parent::literalize($params['start_date'])
			);
			$this->putLine($line);
		}
		return $this->getBody();
	}
}

/* vim:set tabstop=4 ai: */
?>