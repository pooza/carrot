<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * オブジェクト登録設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSObjectRegisterConfigCompiler extends BSConfigCompiler {
	public function execute (BSConfigFile $file) {
		$this->clearBody();
		$this->putLine('$objects = array();');
		foreach ($file->getResult() as $values) {
			$values = new BSArray($values);
			if (BSString::isBlank($values['class'])) {
				throw new BSConfigException('%sで、クラス名が指定されていません。', $file);
			}

			$line = new BSStringFormat('$objects[] = new %s(%s);');
			$line[] = $values['class'];
			$line[] = self::quote((array)$values['params']);
			$this->putLine($line);
		}
		return $this->getBody();
	}
}

/* vim:set tabstop=4: */
