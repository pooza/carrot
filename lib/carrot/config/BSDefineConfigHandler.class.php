<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * DefineConfigHandlerのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSDefineConfigHandler extends BSConfigHandler {
	public function & execute ($path) {
		$prefix = preg_replace('/_$/', '', $this->getParameter('prefix'));
		foreach ($this->getConfig($path) as $category => $values) {
			foreach ($values as $key => $value) {
				if (preg_match('/^\\./', $category)) {
					$key = array($prefix, $key);
				} else {
					$key = array($prefix, $category, $key);
				}
				$key = strtoupper(implode('_', $key));
				if (!defined($key)) {
					$line = sprintf(
						'define(%s, %s);',
						self::literalize($key),
						self::literalize($value)
					);
					$this->putLine($line);
				}
			}
		}
		$body = $this->getBody();
		return $body;
	}
}

/* vim:set tabstop=4 ai: */
?>