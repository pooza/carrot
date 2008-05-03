<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * モジュール設定
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSModuleConfigHandler extends BSConfigHandler {
	public function execute (BSIniFile $file) {
		$this->clearBody();
		$ini = $file->getContents();

		foreach ($ini['module'] as $key => $value) {
			$line = sprintf(
				'$this->config[%s][%s][%s] = %s;',
				parent::literalize('module'),
				parent::literalize('module'),
				parent::literalize($key),
				parent::literalize($value)
			);
			$this->putLine($line);
		}

		return $this->getBody();
	}
}

?>