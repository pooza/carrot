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
		$this->clearBody();
		$expire = $file->getUpdateDate();
		$name = self::getAttributeName($file);
		if ($this->controller->getAttribute($name, $expire) === null) {
			$contents = $this->getContents($file->getResult());
			$this->controller->setAttribute($name, $contents);
		}

		$line = sprintf(
			'$config = BSController::getInstance()->getAttribute(%s);',
			self::quote($name)
		);
		$this->putLine($line);

		return $this->getBody();
	}

	static private function getAttributeName (BSFile $file) {
		$name = $file->getDirectory()->getPath() . DIRECTORY_SEPARATOR . $file->getBaseName();
		$name = str_replace(BS_WEBAPP_DIR, '', $name);
		$name = implode('.', explode(DIRECTORY_SEPARATOR, $name));
		return __CLASS__ . $name;
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
