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
 * @version $Id: BSMenuConfigHandler.class.php 333 2007-06-08 05:48:46Z pooza $
 */
class BSMenuConfigHandler extends IniConfigHandler {
	public function & execute ($config) {
		$body = array(
			'<?php',
			'// auth-generated by ' . get_class($this),
			'// date: ' . BSDate::getNow('Y/m/d H:i:s'),
			'$menu = array();',
		);

		foreach ($this->parseIni($config) as $module => $values) {
			if (!isset($values['HREF'])) {
				if (!isset($values['MODULE'])) {
					$values['MODULE'] = $module;
				}
				if (!isset($values['TITLE'])) {
					$profile = new BSModuleProfile($module);
					$values['TITLE'] = $profile->getConfig('TITLE');
				}
			}

			foreach ($values as $key => $value) {
				$body[] = sprintf(
					'$menu[%s][%s]=%s;',
					$this->literalize($module),
					$this->literalize(strtolower($key)),
					$this->literalize($value)
				);
			}
		}
		$body[] = '?>';
		$body = implode("\n", $body);

		return $body;
	}

	public static function & replaceConstants ($value) {
		return BSDirectoryFinder::replaceConstants($value);
	}
}

/* vim:set tabstop=4 ai: */
?>