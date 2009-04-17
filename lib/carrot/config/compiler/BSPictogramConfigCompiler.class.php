<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * 絵文字用設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPictogramConfigCompiler extends BSDefaultConfigCompiler {

	/**
	 * 設定配列をシリアライズできる内容に修正
	 *
	 * @access protected
	 * @param mixed[] $config 対象
	 * @return mixed[] 変換後
	 */
	protected function getContents ($config) {
		$pictograms = array();
		foreach ((array)$config as $entry) {
			if (!isset($entry['pictogram']['Docomo'])) {
				continue;
			}
			$id = $entry['pictogram']['Docomo'];
			if (isset($entry['names']) && is_array($entry['names'])) {
				foreach ($entry['names'] as $name) {
					$pictograms[$name] = $id;
				}
			}
		}
		return $pictograms;
	}
}

/* vim:set tabstop=4: */
