<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * ディレクトリレイアウト設定
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSLayoutConfigHandler.class.php 205 2008-04-19 11:50:49Z pooza $
 */
class BSLayoutConfigHandler extends BSConfigHandler {
	public function execute ($path) {
		foreach ($this->getConfig($path) as $name => $params) {
			foreach ($params as $key => $value) {
				$line = sprintf(
					'$this->directories[%s][%s] = %s;',
					parent::literalize($name),
					parent::literalize($key),
					parent::literalize($value)
				);
				$line = parent::replaceConstants($line);
				$this->putLine($line);
			}
		}
		return $this->getBody();
	}
}

/* vim:set tabstop=4 ai: */
?>