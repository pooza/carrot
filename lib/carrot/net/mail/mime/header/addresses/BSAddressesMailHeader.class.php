<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime.header.addresses
 */

/**
 * 複数のメールアドレスを格納する抽象メールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSAddressesMailHeader extends BSMailHeader {
	private $addresses;

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return BSMailAddress 実体
	 */
	public function getEntity () {
		if (!$this->addresses) {
			$this->addresses = new BSArray;
		}
		return $this->addresses;
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		$this->addresses = new BSArray;
		$this->appendContents($contents);
	}

	/**
	 * 内容を追加
	 *
	 * @access public
	 * @param string $contents 内容
	 */
	public function appendContents ($contents) {
		$addresses = $this->getEntity();
		if ($contents instanceof BSMailAddress) {
			$addresses[] = $contents;
		} else if (BSArray::isArray($contents)) {
			foreach ($contents as $address) {
				if ($address instanceof BSMailAddress) {
					$addresses[] = $address;
				} else {
					try {
						$addresses[] = new BSMailAddress($address);
					} catch (BSMailException $e) {
					}
				}
			}
		} else {
			$contents = BSMIMEUtility::decode($contents);
			foreach (preg_split('/[;,]/', $contents) as $address) {
				try {
					$addresses[] = new BSMailAddress($address);
				} catch (BSMailException $e) {
				}
			}
		}

		$contents = new BSArray;
		foreach ($addresses as $address) {
			$contents[] = $address->format();
		}
		$this->contents = $contents->join(', ');
	}
}

/* vim:set tabstop=4: */
