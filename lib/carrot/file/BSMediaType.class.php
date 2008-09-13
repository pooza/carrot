<?php
/**
 * @package org.carrot-framework
 * @subpackage file
 */

/**
 * メディアタイプのリスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMediaType extends BSParameterHolder {
	static private $instance;
	private $file;

	/**
	 * @access private
	 */
	private function __construct () {
		$expire = $this->getTypesFile()->getUpdateDate();
		if (!$this->getConfigFile()->getUpdateDate()->isAgo($expire)) {
			$expire = $this->getConfigFile()->getUpdateDate();
		}

		if ($params = BSController::getInstance()->getAttribute(get_class($this), $expire)) {
			$this->setParameters($params);
		} else {
			$this->parse();
			BSController::getInstance()->setAttribute(get_class($this), $this->getParameters());
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSMediaType インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSMediaType;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * mime.typesファイルを返す
	 *
	 * @access private
	 * @return BSFile mime.typesファイル
	 */
	private function getTypesFile () {
		if (!$this->file) {
			if (!$path = BSController::getInstance()->getConstant('TYPES_FILE')) {
				require(BSConfigManager::getInstance()->compile($this->getConfigFile()));
				$path = $config['file'];
			}

			$this->file = new BSFile($path);
			if (!$this->file->isReadable()) {
				throw new BSFileException('"%s"を開くことが出来ません。', $file->getPath());
			}
		}
		return $this->file;
	}

	/**
	 * 設定ファイルを返す
	 *
	 * @access private
	 * @return BSConfigFile 設定ファイル
	 */
	private function getConfigFile () {
		return BSConfigManager::getInstance()->getConfigFile('mime/types');
	}

	/**
	 * 設定ファイルとmime.typesをパース
	 *
	 * @access private
	 */
	private function parse () {
		foreach ($this->getTypesFile()->getLines() as $line) {
			$line = rtrim($line);
			$line = preg_replace('/#.*$/', '', $line);
			$line = preg_split('/[ \t]+/', $line);
			for ($i = 1 ; $i < count($line) ; $i ++) {
				$this->setParameter(strtolower($line[$i]), $line[0]);
			}
		}

		require(BSConfigManager::getInstance()->compile($this->getConfigFile()));
		foreach ($config['types'] as $key => $value) {
			if ($value) {
				$this->setParameter(strtolower($key), $value);
			} else {
				$this->removeParameter($key);
			}
		}
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