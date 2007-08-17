<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * スタイルセット設定
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSStyleSetConfigHandler extends BSConfigHandler {
	public function & execute ($path) {
		foreach ($this->getConfig($path) as $name => $params) {
			foreach ($params['files'] as $key => $value) {
				$line = sprintf(
					'$stylesets[%s][%s][] = %s;',
					self::literalize($name),
					self::literalize('files'),
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