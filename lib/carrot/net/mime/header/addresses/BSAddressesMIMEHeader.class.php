<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mime.header.addresses
 */

/**
 * 複数のメールアドレスを格納する抽象ヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSAddressesMIMEHeader extends BSMIMEHeader {
	private $addresses;

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return BSMailAddress 実体
	 */
	public function getEntity () {
		if (!$this->addresses) {
			$this->addresses = BSArray::create();
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
		$this->addresses = BSArray::create();
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
		} else if (is_array($contents) || ($contents instanceof BSParameterHolder)) {
			foreach ($contents as $address) {
				if ($address instanceof BSMailAddress) {
					$addresses[] = $address;
				} else {
					$addresses[] = BSMailAddress::create($address);
				}
			}
		} else {
			$contents = BSMIMEUtility::decode($contents);
			foreach (mb_split('[;,]', $contents) as $address) {
				if ($address = BSMailAddress::create($address)) {
					$addresses[] = $address;
				}
			}
		}

		$contents = BSArray::create();
		foreach ($addresses as $address) {
			$contents[] = $address->format();
		}
		$this->contents = $contents->join(', ');
	}
}

