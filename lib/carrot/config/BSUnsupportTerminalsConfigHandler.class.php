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
 * @version $Id: BSUnsupportTerminalsConfigHandler.class.php 205 2008-04-19 11:50:49Z pooza $
 */
class BSUnsupportTerminalsConfigHandler extends BSConfigHandler {
	public function execute ($path) {
		foreach ($this->getConfig($path) as $carrier => $params) {
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