<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * オブジェクト登録定義
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSObjectRegisterConfigHandler extends BSConfigHandler {
	public function execute (BSIniFile $file) {
		$this->clearBody();
		foreach ($file->getContents() as $category => $values) {
			if (!isset($values['class'])) {
				throw new BSConfigException(
					'%s のカテゴリー "%s" で、クラス名が指定されていません。',
					$this->getConfigFile(),
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