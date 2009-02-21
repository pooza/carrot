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
		if (!$this->getConfigFile()->getUpdateDate()->isPast($expire)) {
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
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * mime.typesファイルを返す
	 *
	 * @access private
	 * @return BSFile mime.typesファイル
	 */
	private function getTypesFile () {
		if (!$this->file) {
			$this->file = new BSFile(BS_TYPES_FILE);
			if (!$this->file->isReadable()) {
				throw new BSConfigException('%sを開くことが出来ません。', $file);
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
		return BSConfigManager::getInstance()->getConfigFile('mime');
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
				$this[strtolower($line[$i])] = $line[0];
			}
		}

		require(BSConfigManager::getInstance()->compile($this->getConfigFile()));
		foreach ($config['types'] as $key => $value) {
			if ($value) {
				$this[strtolower($key)] = $value;
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

	/**
	 * レンダラーの完全なタイプを返す
	 *
	 * @access public
	 * @param BSRenderer $renderer 対象レンダラー
	 * @return string メディアタイプ
	 * @static
	 */
	static public function getFullContentType (BSRenderer $renderer) {
		if ($renderer instanceof BSTextRenderer) {
			if (BSString::isBlank($charset = mb_preferred_mime_name($renderer->getEncoding()))) {
				throw new BSViewException(
					'エンコード"%s"が正しくありません。',
					$renderer->getEncoding()
				);
			}
			return sprintf('%s; charset=%s', $renderer->getType(), $charset);
		}
		return $renderer->getType();
	}
}

/* vim:set tabstop=4: */
