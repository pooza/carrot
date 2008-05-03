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
	public function execute ($path) {
		$this->clearBody();
		foreach ($this->getConfig($path) as $category => $values) {
			if (!isset($values['class'])) {
				$error = sprintf(
					'%s のカテゴリー "%s" で、クラス名が指定されていません。',
					$this->getConfigFile(),
					$category
				);
				throw new ParseException($error);
			}

			$line = sprintf(
				'$objects[%s] = new %s;',
				parent::literalize($category),
				$values['class']
			);
			$this->putLine($line);

			if ($parameters = parent::parseParameters($values)) {
				$line = sprintf(
					'$objects[%s]->initialize(%s);',
					parent::literalize($category),
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