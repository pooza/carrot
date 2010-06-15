<?php
/**
 * @package org.carrot-framework
 * @subpackage media.convertor
 */

/**
 * 動画の変換
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSMediaConvertor {
	private $name;
	private $config;
	protected $output;

	/**
	 * コンバータの名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		if (!$this->name) {
			if (mb_ereg('^BS(.*)MediaConvertor$', get_class($this), $matches)) {
				$this->name = $matches[1];
			}
		}
		return $this->name;
	}

	/**
	 * 変換後ファイルのサフィックス
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		return '.' . BSString::toLower($this->getName());
	}

	/**
	 * 変換後のクラス名
	 *
	 * @access public
	 * @return string クラス名
	 * @abstract
	 */
	abstract public function getClass ();

	/**
	 * 変換後ファイルのMIMEタイプ
	 *
	 * @access public
	 * @return string MIMEタイプ
	 */
	public function getType () {
		return BSMIMEType::getType($this->getSuffix());
	}

	/**
	 * 変換して返す
	 *
	 * @access public
	 * @param BSMovieFile $source 変換後ファイル
	 * @return BSMediaFile 変換後ファイル
	 */
	public function execute (BSMediaFile $source) {
		$file = BSFileUtility::getTemporaryFile($this->getSuffix());
		if (!$this->isForceExecutable() && ($source->getType() == $this->getType())) {
			$duplicated = $source->copyTo($file->getDirectory());
			$duplicated->rename($file->getName());
			$file = $duplicated;
		} else {
			$command = BSMediaFile::getCommandLine();
			$command->addValue('-y');
			$command->addValue('-i');
			$command->addValue($source->getPath());
			foreach ($this->getConfig() as $key => $value) {
				$command->addValue('-' . $key);
				$command->addValue($value);
			}
			$command->addValue($file->getPath());
			$command->addValue('2>&1', null);
			$this->output = $command->getResult()->join("\n");
			BSLogManager::getInstance()->put($source . 'を変換しました。', $this);
		}
		return BSUtility::executeMethod($this->getClass(), 'search', array($file));
	}

	/**
	 * 強制的に実行するか？
	 *
	 * 変換前後でMIMEタイプが一致する時は、デフォルトでは変換を行わず、単にコピーする。
	 *
	 * @access protected
	 * @return boolean 強制的に実行するならTrue
	 */
	protected function isForceExecutable () {
		return !!$this->getConstant(ltrim($this->getSuffix(), '.') . '_force');
	}

	/**
	 * 全ての設定値を返す
	 *
	 * @access protected
	 * @return BSArray 全ての設定値
	 */
	protected function getConfig () {
		if (!$this->config) {
			$this->config = new BSArray;
			$names =  new BSArray(array(
				'vcodec' => 'video_codec',
				'acodec' => 'audio_codec',
				's' => 'size',
				'r' => 'frame_rate',
				'fs' => 'limit_size',
			));
			foreach ($names as $key => $name) {
				if ($value = $this->getConstant(ltrim($this->getSuffix(), '.') . '_' . $name)) {
					$this->config[$key] = $value;
				}
			}
		}
		return $this->config;
	}

	/**
	 * 定数を返す
	 *
	 * @access protected
	 * @param string $name 定数名
	 * @return string 定数値
	 */
	protected function getConstant ($name) {
		return BSConstantHandler::getInstance()->getParameter('ffmpeg_convert_' . $name);
	}
}

/* vim:set tabstop=4: */
