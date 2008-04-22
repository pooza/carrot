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
 * @version $Id: BSStyleSetConfigHandler.class.php 205 2008-04-19 11:50:49Z pooza $
 */
class BSStyleSetConfigHandler extends BSConfigHandler {
	public function execute ($path) {
		foreach ($this->getConfig($path) as $name => $params) {
			foreach ($params['files'] as $key => $value) {
				$line = sprintf(
					'$stylesets[%s][%s][] = %s;',
					parent::literalize($name),
					parent::literalize('files'),
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