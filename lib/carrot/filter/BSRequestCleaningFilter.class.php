<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * リクエストクリーニングフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRequestCleaningFilter extends BSFilter {
	public function initialize ($parameters = array()) {
		$this->setParameter('convert_kana', 'KV');
		$this->setParameter('new_line', false);
		$this->setParameter('reading', false);
		$this->setParameter('date', false);
		return parent::initialize($parameters);
	}

	public function execute (BSFilterChain $filters) {
		foreach ($this->request->getParameters() as $key => $value) {
			$value = BSString::convertEncoding($value);
			$value = str_replace("\0", '', $value);

			if (!BSArray::isArray($value) && get_magic_quotes_gpc()) {
				$value = stripslashes($value);
			}

			if ($this->getParameter('new_line')) {
				$value = str_replace("\r\n", "\n", $value);
				$value = str_replace("\r", "\n", $value);
			}

			if ($pattern = $this->getParameter('convert_kana')) {
				$value = BSString::convertKana($value, $pattern);
			}

			if ($this->getParameter('reading')) {
				if (preg_match('/_read$/', $key)) {
					$value = str_replace(' ', '', $value);
					$value = BSString::convertKana($value, 'KVC');
				}
			}

			if ($this->getParameter('date')) {
				if (!BSArray::isArray($value) && preg_match('/(day|date)$/', $key)) {
					try {
						$date = new BSDate($value);
						$value = $date->format('Y-m-d');
					} catch (BSDateException $e) {
					}
				}
			}

			$this->request->setParameter($key, $value);
		}
		$filters->execute();
	}
}

/* vim:set tabstop=4 ai: */
?>