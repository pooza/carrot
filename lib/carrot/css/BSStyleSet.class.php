<?php
/**
 * @package org.carrot-framework
 * @subpackage css
 */

BSUtility::includeFile('pear/HTML/CSS.php');

/**
 * スタイルセット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSStyleSet extends HTML_CSS implements BSTextRenderer {
	private $name;
	private $files = array();
	private $rules = array();
	private $contents;
	static private $stylesets = array();

	/**
	 * @access public
	 * @param string $styleset スタイルセット名
	 */
	public function __construct ($styleset = 'carrot') {
		$this->name = $styleset;
		$this->setCharset();

		$stylesets = self::getStyleSets();
		$files = array();
		$dir = BSController::getInstance()->getDirectory('css');
		if (isset($stylesets[$styleset]['files'])) {
			foreach ($stylesets[$styleset]['files'] as $file) {
				if ($entry = $dir->getEntry($file)) {
					$files[] = $entry;
				}
			}
		} else if ($entry = $dir->getEntry($styleset)) {
			$files[] = $entry;
		}

		foreach ($files as $file) {
			$this->parseFile($file);
		}
	}

	/**
	 *スタイルセット名を返す
	 *
	 * @access public
	 * @return string スタイルセット名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		if (!$this->contents) {
			if (!$this->files) {
				throw new BSCSSException('スタイルセットを構成するファイルがありません。');
			}

			$date = new BSDate;
			foreach ($this->files as $file) {
				if ($date->getTimestamp() < $file->getUpdateDate()->getTimestamp()) {
					$date = $file->getUpdateDate();
				}
			}

			$name = sprintf('%s.%s', get_class($this), $this->getName());
			if (!$this->contents = BSController::getInstance()->getAttribute($name, $date)) {
				foreach ($this->getRules() as $key => $value) {
					$this->contents .= sprintf("@%s \"%s\";\n", $key, $value);
				}
				$this->contents .= $this->toString();
				BSController::getInstance()->setAttribute($name, $this->contents);
			}
		}
		return $this->contents;
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMediaType::getType('css');
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return $this->getRule('charset');
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return !$this->isError() && $this->getContents();
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->_lastError;
	}

	/**
	 * 文字セットを設定
	 *
	 * @access public
	 * @param string $type 文字セットの種類
	 * @return PEAR_Error エラーが発生した場合はエラーオブジェクトを返す
	 */
	public function setCharset ($type = 'utf-8') {
		$this->setRule('charset', $type);
		return parent::setCharset($type);
	}

	/**
	 * 定義済み@規則を返す
	 *
	 * @access protected
	 * @param string $name @規則の名前
	 * @return string @規則の値
	 */
	protected function getRule ($name) {
		if (isset($this->rules[$name])) {
			return $this->rules[$name];
		}
	}

	/**
	 * 定義済み@規則を全て返す
	 *
	 * @access protected
	 * @return string @規則の値
	 */
	protected function getRules () {
		return $this->rules;
	}

	/**
	 * @規則を設定
	 *
	 * ごく単純な、文字列で指定するものだけ
	 *
	 * @access protected
	 * @param string $name @規則の名前
	 * @param string $value @規則の値
	 */
	protected function setRule ($name, $value) {
		if ($value != '') {
			$this->rules[$name] = $value;
		} else {
			unset($this->rules[$name]);
		}
	}

	/**
	 * CSS文字列をパース
	 *
	 * @access public
	 * @param string $str CSS文字列
	 * @param boolean $duplicates 重複を許すか？
	 */
	public function parseString ($str, $duplicates = false) {
		// "@charset" 等を保護
		if (preg_match('/@(charset) *[\'"]?([^;\'"]+)[\'"]?;/', $str, $matches)) {
			$this->setRule($matches[1], $matches[2]);
			$str = str_replace($matches[0], null, $str);
		}

		parent::parseString($str, $duplicates);
	}

	/**
	 * CSSファイルを登録
	 *
	 * @access public
	 * @param BSFile|string $file CSSファイル、又はその名前
	 * @param boolean $duplicates 重複を許すか？
	 */
	public function parseFile ($file, $duplicates = false) {
		if ($file instanceof BSFile) {
			// 素通り
		} else if (BSUtility::isPathAbsolute($file)) {
			$file = new BSFile($file);
		} else if ($entry = BSController::getInstance()->getDirectory('css')->getEntry($file)) {
			$file = $entry;
		} else {
			throw new BSCSSException('CSSファイル "%s" が読み込めません。', $file);
		}

		if ($this->addFile($file)) {
			if (BSRequest::getInstance()->getUserAgent()->hasBug('css')) {
				return;
			} else if ($error = parent::parseFile($file->getPath(), $duplicates)) {
				if ($error instanceof PEAR_Error) {
					throw new BSCSSException($error->getMessage());
				} else {
					throw new BSCSSException('原因不明のエラーが発生。');
				}
			}
		}
	}

	/**
	 * 登録ファイルを配列に代入
	 *
	 * @access private
	 * @param BSFile $file CSSファイル又はINIファイル
	 * @return boolean 代入を実際に行ったらTrue
	 */
	private function addFile (BSFile $file) {
		if (!$file->isReadable()) {
			throw new BSCSSException('%sが読み込めません。', $file);
		}
		foreach ($this->files as $fileCurrent) {
			if ($file->getPath() == $fileCurrent->getPath()) {
				return false;
			}
		}
		$this->files[] = $file;
		return true;
	}

	/**
	 * 全てのスタイルセットを返す
	 *
	 * @access private
	 * @return string[][] スタイルセットを配列で返す
	 * @static
	 */
	static private function getStyleSets () {
		if (!self::$stylesets) {
			require(BSConfigManager::getInstance()->compile('styleset/application'));
			self::$stylesets += $config;
			require(BSConfigManager::getInstance()->compile('styleset/carrot'));
			self::$stylesets += $config;
		}
		return self::$stylesets;
	}

	/**
	 * 全てのスタイルセットの名前を返す
	 *
	 * @access public
	 * @return string[] スタイルセットの名前を配列で返す
	 * @static
	 */
	static public function getStyleSetNames () {
		$names = array_keys(self::getStyleSets());
		$names[] = 'carrot';
		sort($names);
		return $names;
	}
}

/* vim:set tabstop=4: */
