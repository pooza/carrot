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
	public function execute (BSIniFile $file) {
		$this->clearBody();
		$dates = new BSArray;
		foreach ($file->getContents() as $gengo => $params) {
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