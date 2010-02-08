<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * 規定設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDefaultConfigCompiler extends BSConfigCompiler {
	public function execute (BSConfigFile $file) {
		if ($this->controller->getAttribute($file, $file->getUpdateDate()) === null) {
			$this->controller->setAttribute($file, $this->getContents($file->getResult()));
		}

		$this->clearBody();
		$line = sprintf(
			'return BSController::getInstance()->getAttribute(%s);',
			self::quote(BSSerializeHandler::getInstance()->serializeName($file))
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
		return $config;
	}
}

/* vim:set tabstop=4: */
