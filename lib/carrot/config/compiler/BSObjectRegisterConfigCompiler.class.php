<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * オブジェクト登録設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSObjectRegisterConfigCompiler extends BSConfigCompiler {
	public function execute (BSConfigFile $file) {
		$this->clearBody();
		foreach ($file->getResult() as $category => $values) {
			if (!isset($values['class'])) {
				throw new BSConfigException(
					'%s の "%s" で、クラス名が指定されていません。',
					$file,
					$category
				);
			}

			$line = sprintf('$objects[%s] = new %s;', self::quote($category), $values['class']);
			$this->putLine($line);

			if (isset($values['params']) && BSArray::isArray($values['params'])) {
				$params = parent::parseParameters($values['params'], null);
			} else {
				$params = parent::parseParameters($values);
			}
			$line = sprintf('$objects[%s]->initialize(%s);', self::quote($category), $params);
			$this->putLine($line);
		}
		return $this->getBody();
	}
}

/* vim:set tabstop=4 ai: */
?>