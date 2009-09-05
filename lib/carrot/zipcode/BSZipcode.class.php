<?php
/**
 * @package org.carrot-framework
 * @subpackage zipcode
 */

/**
 * 郵便番号
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSZipcode implements BSAssignable {
	private $contents;
	private $major;
	private $minor;
	private $file;
	private $info;
	private $pref;
	const PATTERN = '/^([0-9]{3})\-?([0-9]{4})$/i';

	/**
	 * @access public
	 * @param string $value 内容
	 */
	public function __construct ($value) {
		if (!preg_match(self::PATTERN, $value, $matches)) {
			throw new BSZipcodeException('"%s" は正しい郵便番号ではありません。', $value);
		}
		$this->major = $matches[1];
		$this->minor = $matches[2];
	}

	/**
	 * 郵便番号を返す
	 *
	 * @access public
	 * @return string 郵便番号
	 */
	public function getContents () {
		if (!$this->contents) {
			$this->contents = sprintf('%s-%s', $this->major, $this->minor);
		}
		return $this->contents;
	}

	/**
	 * JSONファイルを返す
	 *
	 * @access public
	 * @return BSFile JSONファイル
	 */
	public function getFile () {
		if (!$this->file) {
			$dir = BSController::getInstance()->getDirectory('zipcode');
			$this->file = $dir->getEntry('zip-' . $this->major);
		}
		return $this->file;
	}

	/**
	 * 住所情報を取得する
	 *
	 * @access private
	 * @return BSArray 住所情報
	 */
	private function getInfo () {
		if (!$this->info && $this->getFile()) {
			$serializer = new BSJSONSerializer;
			$addresses = new BSArray($serializer->decode($this->getFile()->getContents()));
			if ($info = $addresses[$this->major. $this->minor]) {
				$this->info = new BSArray($info);
			}
		}
		return $this->info;
	}

	/**
	 * 都道府県を返す
	 *
	 * @access public
	 * @return string 都道府県
	 */
	public function getPref () {
		if (!$this->pref && $this->getInfo()) {
			require(BSConfigManager::getInstance()->compile('postal'));
			$this->pref = $config['prefs'][$this->getInfo()->getParameter(0) - 1];
		}
		return $this->pref;
	}

	/**
	 * 市区町村を返す
	 *
	 * @access public
	 * @return string 市区町村
	 */
	public function getCity () {
		if ($this->getInfo()) {
			return $this->getInfo()->getParameter(1);
		}
	}

	/**
	 * 町域を返す
	 *
	 * @access public
	 * @return string 町域
	 */
	public function getLocality () {
		if ($this->getInfo()) {
			return $this->getInfo()->getParameter(2);
		}
	}

	/**
	 * 住所を返す
	 *
	 * @access public
	 * @return string 住所
	 */
	public function getAddress () {
		return $this->getPref() . $this->getCity() . $this->getLocality();
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->getContents();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('郵便番号 "%s"', $this->getContents());
	}
}

/* vim:set tabstop=4: */
