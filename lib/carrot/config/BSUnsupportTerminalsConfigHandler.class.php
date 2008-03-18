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
	public function & execute ($path) {
		foreach ($this->getConfig($path) as $carrier => $params) {
			foreach ($params['terminals'] as $terminal) {
				$line = sprintf(
					'$terminals[%s][] = %s;',
					self::literalize($carrier),
					self::literalize($terminal)
				);
				$this->putLine($line);
			}
		}
		$body = $this->getBody();
		return $body;
	}
}

/* vim:set tabstop=4 ai: */
?>