<?php
/**
 * @package org.carrot-framework
 * @subpackage service.oauth
 */

/**
 * Authorizationヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAuthorizationMIMEHeader extends BSMIMEHeader {
	protected $name = 'Authorization';
	private $signature;

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if ($contents instanceof BSOAuthSignature) {
			$this->setSignature($contents);
		} else {
			foreach (BSString::explode(',', $contents) as $field) {
				$field = trim($field);
				if (mb_ereg('([^=]+)="([^"]+)"', $field, $matches)) {
					$this[BSURL::decode($matches[1])] = BSURL::decode($matches[2]);
				}
			}
		}
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		$values = new BSArray;
		$values['OAuth realm'] = 'OAuth realm=""';

		$params = new BSArray($this->getParameters());
		$params->sort();
		foreach ($params as $key => $value) {
			$field = new BSStringFormat('%s="%s"');
			$field[] = BSURL::encode($key);
			$field[] = BSURL::encode($value);
			$values[$key] = $field->getContents();
		}
		return $values->join(', ');
	}

	/**
	 * シグネチャを設定
	 *
	 * @access public
	 * @param BSOAuthSignature $signature シグネチャ
	 */
	public function setSignature (BSOAuthSignature $signature) {
		$this->signature = $signature;
		$this->setParameters($signature);
		$this['oauth_signature'] = $signature->getContents();
	}
}

/* vim:set tabstop=4: */
