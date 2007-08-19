<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * 管理メニュー設定
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSMenuConfigHandler extends BSConfigHandler {
	public function & execute ($path) {
		foreach ($this->getConfig($path) as $module => $values) {
			if (!isset($values['HREF'])) {
				if (!isset($values['MODULE'])) {
					$values['MODULE'] = $module;
				}
				if (!isset($values['TITLE'])) {
					$profile = $this->controller->getModuleProfile($module);
					$values['TITLE'] = $profile->getConfig('TITLE');
				}
			}
			foreach ($values as $key => $value) {
				$line = sprintf(
					'$menu[%s][%s]=%s;',
					self::literalize($module),
					self::literalize(strtolower($key)),
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