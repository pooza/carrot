<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * 絵文字用設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPictogramConfigCompiler extends BSDefaultConfigCompiler {
	public function execute (BSConfigFile $file) {
		// $file->serialize()が使用できない為、直接シリアライズ
		if ($this->controller->getAttribute($file, $file->getUpdateDate()) === null) {
			$this->controller->setAttribute($file, $this->getContents($file->getResult()));
		}

		$this->clearBody();
		$line = sprintf(
			'return BSController::getInstance()->getAttribute(%s);',
			self::quote($file->digestSerialized())
		);
		$this->putLine($line);
		return $this->getBody();
	}

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
			foreach ($entry['names'] as $name) {
				$pictograms['codes'][$name] = $entry['pictograms'];
				$code = $entry['pictograms'][BSMobileCarrier::DEFAULT_CARRIER];
				$pictograms['names'][$code][] = $name;
			}
		}
		return $pictograms;
	}
}

/* vim:set tabstop=4: */
