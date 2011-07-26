<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * 規定設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDefaultConfigCompiler extends BSConfigCompiler {
	public function execute (BSConfigFile $file) {
		// $file->serialize()が使用できないケースがある為、直接シリアライズ
		if ($this->controller->getAttribute($file, $file->getUpdateDate()) === null) {
			$this->controller->setAttribute($file, $this->getContents($file->getResult()));
		}

		$this->clearBody();
		$line = sprintf('return %s;', self::quote($this->controller->getAttribute($file)));
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
		return $config;
	}
}

/* vim:set tabstop=4: */
