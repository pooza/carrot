<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * モジュール設定
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSerializeConfigHandler extends BSConfigHandler {
	public function execute (BSIniFile $file) {
		$this->clearBody();
		$expire = $file->getUpdateDate();
		$name = self::getAttributeName($file);
		if ($this->controller->getAttribute($name, $expire) === null) {
			$contents = $this->getContents($file->getContents());
			$this->controller->setAttribute($name, $contents);
		}

		$line = sprintf(
			'$config = BSController::getInstance()->getAttribute(%s);',
			self::quote($name)
		);
		$this->putLine($line);

		return $this->getBody();
	}

	private static function getAttributeName (BSFile $file) {
		$name = $file->getDirectory()->getPath() . '/' . $file->getBaseName();
		$name = str_replace(BSController::getInstance()->getPath('webapp'), '', $name);
		$name = implode('.', explode('/', $name));
		return __CLASS__ . $name;
	}

	/**
	 * 設定配列をシリアライズできる内容に修正
	 *
	 * @access protected
	 * @param mixed[] $config 対象
	 * @return mixed[] 変換後
	 */
	protected function getContents ($config) {
		return $config;
	}
}

?>