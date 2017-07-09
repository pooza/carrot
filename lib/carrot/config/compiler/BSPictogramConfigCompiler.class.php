<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config.compiler
 */

/**
 * 絵文字用設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
		$pictograms = [];
		foreach (BSArray::create($config) as $entry) {
			foreach ($entry['names'] as $name) {
				$pictograms['codes'][$name] = $entry['pictograms'];
				$pictograms['names'][$entry['pictograms']['Docomo']][] = $name;
			}
		}
		return $pictograms;
	}
}

