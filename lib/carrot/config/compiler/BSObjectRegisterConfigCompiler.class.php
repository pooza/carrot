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
		foreach ($file->getResult() as $values) {
			$values = new BSArray($values);
			if (!$values['class']) {
				throw new BSConfigException('%sで、クラス名が指定されていません。', $file);
			}

			$line = new BSStringFormat('$object = new %s;');
			$line[] = $values['class'];
			$this->putLine($line);

			$line = new BSStringFormat('$object->initialize(%s);');
			$line[] = self::quote((array)$values['params']);
			$this->putLine($line);

			$this->putLine('$objects[] = $object;');
		}
		return $this->getBody();
	}
}

/* vim:set tabstop=4: */
