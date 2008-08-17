<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * メニュー設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSMenuConfigCompiler extends BSSerializeConfigCompiler {

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
			if (BSArray::isArray($values)) {
				foreach ($values as $key => $value) {
					unset($values[$key]);
					$values[strtolower($key)] = parent::replaceConstants($value);
				}
			}
			if (preg_match('/^\-/', $module)) {
				$values['title'] = '---';
			} else if (!isset($values['href'])) {
				if (!isset($values['module'])) {
					$values['module'] = $module;
				}
				if (!isset($values['title'])) {
					$values['title'] = $this->controller->getModule($module)->getConfig('title');
				}
			}
			$contents[] = $values;
		}
		return $contents;
	}
}

/* vim:set tabstop=4 ai: */
?>