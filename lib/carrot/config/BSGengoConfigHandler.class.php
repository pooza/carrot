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
 * @version $Id$
 */
class BSGengoConfigHandler extends BSConfigHandler {
	public function & execute ($path) {
		$dates = new BSArray;
		foreach ($this->getConfig($path) as $gengo => $params) {
			$line = sprintf(
				'self::$japaneseCalendarDates[%s] = %s;',
				self::literalize($gengo),
				self::literalize($params['start_date'])
			);
			$this->putLine($line);
		}
		$body = $this->getBody();
		return $body;
	}
}

/* vim:set tabstop=4 ai: */
?>