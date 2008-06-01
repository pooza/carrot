<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file
 */

/**
 * メディアタイプのリスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSTypeList extends BSParameterHolder {
	static private $instance;
	private $file;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$expire = $this->getFile()->getUpdateDate();
		if ($params = BSController::getInstance()->getAttribute(get_class($this), $expire)) {
			$this->setParameters($params);
		} else {
			foreach ($this->getFile()->getLines() as $line) {
				$line = preg_replace('/#.*$/', '', $line);
				$line = preg_split('/[ \t]+/', $line);
				for ($i = 1 ; $i < count($line) ; $i ++) {
					$this->setParameter($line[$i], $line[0]);
				}
			}
			BSController::getInstance()->setAttribute(get_class($this), $this->getParameters());
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSTypeList インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSTypeList;
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
			$this->file = new BSFile(BS_TYPES_FILE);
			if (!$this->file->isReadable()) {
				throw new BSFileException('"%s"を開くことが出来ません。', $file->getPath());
			}
		}
		return $this->file;
	}

	/**
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @return mixed パラメータ
	 */
	public function getParameter ($name) {
		$name = preg_replace('/^\./', '', $name);
		return parent::getParameter($name);
	}

	/**
	 * 規定のメディアタイプを返す
	 *
	 * @access public
	 * @param string $suffix サフィックス
	 * @return string メディアタイプ
	 * @static
	 */
	static public function getType ($suffix) {
		if (!$type = self::getInstance()->getParameter($suffix)) {
			$type = 'application/octet-stream';
		}
		return $type;
	}
}

/* vim:set tabstop=4 ai: */
?>