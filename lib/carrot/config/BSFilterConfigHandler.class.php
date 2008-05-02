<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * フィルタ定義
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSFilterConfigHandler extends BSConfigHandler {
	public function execute ($path) {
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
				'$filters[%s] = new %s();',
				parent::literalize($category),
				$values['class']
			);
			$this->putLine($line);

			$line = sprintf(
				'$filters[%s]->initialize(%s);',
				parent::literalize($category),
				parent::parseParameters($values)
			);
			$this->putLine($line);
		}
		return $this->getBody();
	}
}

/* vim:set tabstop=4 ai: */
?>