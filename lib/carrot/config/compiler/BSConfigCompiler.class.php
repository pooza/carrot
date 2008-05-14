<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * 抽象設定ハンドラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
abstract class BSConfigCompiler extends ParameterHolder {
	private $body;

	/**
	 * プロパティ取得のオーバライド
	 *
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
				return BSController::getInstance();
			case 'request':
				return BSRequest::getInstance();
			case 'user':
				return BSUser::getInstance();
		}
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ
	 * @return boolean 成功ならばTrue
	 * @static
	 */
	public function initialize ($parameters = null) {
		if ($parameters) {
			$this->parameters = array_merge($this->parameters, $parameters);
		}
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param BSConfigFile $file 設定ファイル
	 * @abstract
	 */
	abstract public function execute (BSConfigFile $file);

	/**
	 * コンパイル後のphpステートメントを返す
	 *
	 * @access public
	 * @return string コンパイル結果
	 */
	protected function getBody () {
		return $this->body->join("\n");
	}

	/**
	 * phpステートメントを初期化
	 *
	 * @access protected
	 */
	protected function clearBody () {
		$this->body = new BSArray;
		$this->putLine('<?php');
		$this->putLine('// auth-generated by ' . get_class($this));
		$this->putLine('// date: ' . date('Y/m/d H:i:s'));
	}

	/**
	 * phpステートメントの末尾に1行追加
	 *
	 * @access public
	 * @param string $line phpステートメント
	 */
	protected function putLine ($line) {
		$this->body[] = $line;
	}

	/**
	 * 文字列のクォート
	 *
	 * @access public
	 * @param string $value 置換対象
	 * @return string 置換結果
	 * @static
	 */
	public static function quote ($value) {
		$value = trim($value);
		switch (strtolower($value)) {
			case null:
				return 'null';
			case 'on':
			case 'yes':
			case 'true':
				return 'true';
			case 'off':
			case 'no':
			case 'false':
				return 'false';
			default:
				if (is_numeric($value)) {
					return $value;
				} else {
					$value = str_replace("\\", "\\\\", $value);
					$value = str_replace("%'", "\"", $value);
					$value = str_replace("'", "\\'", $value);
					return "'" . $value . "'";
				}
		}
	}

	/**
	 * 定数で置換
	 *
	 * @access public
	 * @param string $value 置換対象
	 * @return string 置換結果
	 * @static
	 */
	public static function replaceConstants ($value) {
		$value = str_replace('%%', '##PERCENT##', $value);
		while (preg_match('/%([A-Z0-9_]+)%/', $value, $matches)) {
			$value = str_replace($matches[0], constant($matches[1]), $value);
		}
		$value = str_replace('##PERCENT##', '%', $value);
		return $value;
	}

	/**
	 * パラメータ配列をPHPスクリプトにパースする
	 *
	 * @access public
	 * @param string $values パラメータ配列
	 * @param string $prefix パラメータ名プリフィックス
	 * @return string PHPスクリプト
	 * @static
	 */
	public static function parseParameters ($values, $prefix = 'param') {
		$body = new BSArray;
		$pattern = '/^' . preg_quote($prefix) . '\.([0-9a-z_]+)/i';

		foreach ($values as $key => $value) {
			if (!preg_match($pattern, $key, $matches)) {
				continue;
			}
			if (is_array($value)) {
				$body[] = sprintf('%s => array(%s)',
					self::quote($matches[1]),
					implode(', ', $value)
				);
			} else {
				$body[] = sprintf(
					'%s => %s',
					self::quote($matches[1]),
					self::quote($value)
				);
			}
		}

		if (0 < count($body)) {
			return sprintf('array(%s)', $body->join(', '));
		}
	}
}

/* vim:set tabstop=4 ai: */
?>