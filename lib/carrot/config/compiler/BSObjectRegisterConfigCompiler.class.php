<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
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
					'%s のカテゴリー "%s" で、クラス名が指定されていません。',
					$file,
					$category
				);
			}

			$line = sprintf(
				'$objects[%s] = new %s;',
				self::quote($category),
				$values['class']
			);
			$this->putLine($line);

			if ($parameters = parent::parseParameters($values)) {
				$line = sprintf(
					'$objects[%s]->initialize(%s);',
					self::quote($category),
					$parameters
				);
				$this->putLine($line);
			}
		}
		return $this->getBody();
	}
}

/* vim:set tabstop=4 ai: */
?>