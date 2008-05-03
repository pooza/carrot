<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * 非対応端末設定
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSUnsupportTerminalsConfigHandler extends BSConfigHandler {
	public function execute (BSIniFile $file) {
		$this->clearBody();
		foreach ($file->getContents() as $carrier => $params) {
			foreach ($params['terminals'] as $terminal) {
				$line = sprintf(
					'$terminals[%s][] = %s;',
					parent::literalize($carrier),
					parent::literalize($terminal)
				);
				$this->putLine($line);
			}
		}
		return $this->getBody();
	}
}

/* vim:set tabstop=4 ai: */
?>