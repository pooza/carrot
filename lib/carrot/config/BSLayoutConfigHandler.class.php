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
	public function execute (BSIniFile $file) {
		$this->clearBody();
		foreach ($file->getContents() as $name => $params) {
			foreach ($params as $key => $value) {
				$line = sprintf(
					'$this->directories[%s][%s] = %s;',
					self::quote($name),
					self::quote($key),
					self::quote($value)
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