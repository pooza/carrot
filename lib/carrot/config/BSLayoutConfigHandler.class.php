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
 * @version $Id$
 */
class BSLayoutConfigHandler extends BSConfigHandler {
	public function & execute ($path) {
		foreach ($this->getConfig($path) as $name => $params) {
			foreach ($params as $key => $value) {
				$line = sprintf(
					'$this->directories[%s][%s] = %s;',
					self::literalize($name),
					self::literalize($key),
					self::literalize($value)
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