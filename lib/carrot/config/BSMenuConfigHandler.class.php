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
class BSMenuConfigHandler extends BSSerializeConfigHandler {

	/**
	 * 設定配列をシリアライズできる内容に修正
	 *
	 * @access protected
	 * @param mixed[] $config 対象
	 * @return mixed[] 変換後
	 */
	protected function getContents ($config) {
		$contents = array();
		foreach ($config as $module => $values) {
			if (!isset($values['HREF'])) {
				if (!isset($values['MODULE'])) {
					$values['MODULE'] = $module;
				}
				if (!isset($values['TITLE'])) {
					$profile = $this->controller->getModule($module);
					$values['TITLE'] = $profile->getConfig('TITLE');
				}
			}
			foreach ($values as $key => $value) {
				$value = parent::replaceConstants($value);
				$contents[$module][strtolower($key)] = $value;
			}
		}
		return $contents;
	}
}

/* vim:set tabstop=4 ai: */
?>