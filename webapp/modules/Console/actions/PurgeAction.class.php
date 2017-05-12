<?php
/**
 * Purgeアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class PurgeAction extends BSAction {
	public function execute () {
		$dirs = BSDirectoryLayout::getInstance();
		foreach ($dirs->getEntries() as $name => $values) {
			if ($values['purge']) {
				$date = BSDate::create();
				foreach ($values['purge'] as $key => $value) {
					$date[$key] = '-' . $value;
				}
				$dirs[$name]->purge($date);
			}
		}
	}
}

