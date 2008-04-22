<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net
 */

/**
 * ネットワークサービスのリスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSServiceList.class.php 5 2007-07-25 08:04:01Z pooza $
 */
class BSServiceList extends BSList {
	private static $instance;
	private $file;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化禁止
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSServiceList インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSServiceList();
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 設定ファイルを返す
	 *
	 * @access public
	 * @return BSFile 設定ファイル
	 */
	public function getFile () {
		if (!$this->file) {
			$file = new BSFile(BS_SERVICES_FILE);
			if (!$file->isReadable()) {
				throw new BSNetException('"%s"を開くことが出来ません。', $file->getPath());
			}
			$this->setFile($file);
		}
		return $this->file;
	}

	/**
	 * 設定ファイルを設定する
	 *
	 * @access public
	 * @param BSFile $file 設定ファイル 
	 */
	public function setFile (BSFile $file) {
		$this->file = $file;
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = BSController::getInstance()->getAttribute(
				$this->getName(), $this->getFile()->getUpdateDate()
			);
			if (!$this->attributes) {
				$ptn = '/^([a-z-]+)[ \t]+([0-9]+)\/tcp/';
				foreach ($this->getFile()->getLines() as $line) {
					if (preg_match($ptn, $line, $matches) && $matches[1] && $matches[2]) {
						$this->attributes[trim($matches[1])] = trim($matches[2]);
					}
				}
				BSController::getInstance()->setAttribute(
					$this->getName(), $this->attributes
				);
			}
		}
		return $this->attributes;
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @param string $service サービス名
	 * @return integer ポート番号
	 * @static
	 */
	public static function getPort ($service) {
		return self::getInstance()->getAttribute($service);
	}
}

/* vim:set tabstop=4 ai: */
?>