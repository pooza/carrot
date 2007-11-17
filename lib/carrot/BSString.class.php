<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * 文字列に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSString {
	const SCRIPT_ENCODING = BS_SCRIPT_ENCODING;
	const TEMPLATE_ENCODING = BS_SMARTY_TEMPLATE_ENCODING;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化は禁止
	}

	/**
	 * 文字コード変換
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @param string $encodingTo 変換後文字セット名
	 * @param string $encodingFrom 変換前文字セット名
	 * @return mixed 変換後
	 * @static
	 */
	public static function convertEncoding ($value, $encodingTo = null, $encodingFrom = null) {
		if (is_array($value)) {
			foreach ($value as &$item) {
				$item = self::convertEncoding($item, $encodingTo, $encodingFrom);
			}
		} else {
			if (!$encodingTo) {
				$encodingTo = self::SCRIPT_ENCODING;
			}
			if (!$encodingFrom) {
				$encodingFrom = 'utf-8,euc-jp,sjis,jis';
			}
			if ($encodingFrom != $encodingTo) {
				$value = mb_convert_encoding($value, $encodingTo, $encodingFrom);
			}
		}
		return $value;
	}

	/**
	 * 文字コードを返す
	 *
	 * @access public
	 * @param string $str 評価対象の文字列
	 * @return string 文字コード
	 * @static
	 */
	public static function getEncoding ($str) {
		return strtolower(mb_detect_encoding($str, 'ascii,utf-8,euc-jp,sjis,jis'));
	}

	/**
	 * 文字列のサニタイズ
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	public static function sanitize ($value) {
		if (is_array($value)) {
			foreach ($value as &$item) {
				$item = self::sanitize($item);
			}
		} else {
			$value = htmlspecialchars($value, ENT_QUOTES);
		}
		return $value;
	}

	/**
	 * サニタイズされた文字列を元に戻す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	public static function unsanitize ($value) {
		if (is_array($value)) {
			foreach ($value as &$item) {
				$item = self::unsanitize($item);
			}
		} else {
			$value = htmlspecialchars_decode($value, ENT_QUOTES);
		}
		return $value;
	}

	/**
	 * 全角・半角を標準化
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @param string $format 変換の形式
	 * @return mixed 変換後
	 * @static
	 */
	public static function convertKana ($value, $format = 'KVa') {
		if (is_array($value)) {
			foreach ($value as &$item) {
				$item = self::convertKana($item, $format);
			}
		} else {
			$value = mb_convert_kana($value, $format, self::SCRIPT_ENCODING);
		}
		return $value;
	}

	/**
	 * キャピタライズされた文字列を返す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	public static function capitalize ($value) {
		if (is_array($value)) {
			foreach ($value as &$item) {
				$item = self::capitalize($item);
			}
		} else {
			$value = ucfirst(strtolower($value));
		}
		return $value;
	}

	/**
	 * Camel化された文字列を返す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	public static function camelize ($value) {
		if (is_array($value)) {
			foreach ($value as &$item) {
				$item = self::camelize($item);
			}
		} else {
			if ($parts = preg_split('/[_ ]/', $value)) {
				$dest = strtolower(array_shift($parts));
				foreach ($parts as $part) {
					$dest .= self::capitalize($part);
				}
				$value = $dest;
			}
		}
		return $value;
	}

	/**
	 * Palcal化された文字列を返す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	public static function pascalize ($value) {
		if (is_array($value)) {
			foreach ($value as &$item) {
				$item = self::pascalize($item);
			}
		} else {
			$dest = '';
			foreach (preg_split('/[_ ]/', $value) as $part) {
				$dest .= self::capitalize($part);
			}
			$value = $dest;
		}
		return $value;
	}

	/**
	 * アンダースコア化された文字列を返す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	public static function underscorize ($value) {
		if (is_array($value)) {
			foreach ($value as &$item) {
				$item = self::underscorize($item);
			}
		} else {
			foreach (array('/[A-Z][a-z0-9]+/', '/[A-Z]{2,}/', '/[A-Z]/') as $pattern) {
				while (preg_match($pattern, $value, $matches)) {
					$word = $matches[0];
					$value = str_replace($word, '_' . strtolower($word), $value);
				}
			}
			$value = preg_replace('/_+/', '_', $value);
			$value = preg_replace('/^_/', '', $value);
			$value = strtolower($value);
		}
		return $value;
	}

	/**
	 * 指定幅で折り畳む
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @param integer $witdh 半角単位での行幅
	 * @return string 変換後の文字列
	 * @static
	 */
	public static function split ($str, $width = 74) {
		$str = self::convertEncoding($str, 'eucjp-win', self::SCRIPT_ENCODING);

		BSController::includeLegacy('/OME/OME.php');
		mb_internal_encoding('eucjp-win');
		$ome = new OME();
		$ome->setBodyWidth($width);
		$str = @$ome->devideWithLimitingWidth($str);
		mb_internal_encoding(self::SCRIPT_ENCODING);

		$str = self::convertEncoding($str, self::SCRIPT_ENCODING, 'eucjp-win');
		return $str;
	}

	/**
	 * 引用文に整形
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @param integer $witdh 半角単位での行幅
	 * @param string $prefix 行頭記号
	 * @return string 変換後の文字列
	 * @static
	 */
	public static function cite ($str, $width = 74, $prefix = '> ') {
		$str = self::split($str, $width - strlen($prefix));
		$lines = explode("\n", $str);
		foreach ($lines as &$line) {
			$line = $prefix . $line;
		}
		return implode("\n", $lines);
	}

	/**
	 * 文字列に変換する
	 *
	 * @access public
	 * @param $mixed[] $value 変換対象
	 * @param string $fieldGlue キーと値の間に入る文字列
	 * @param string $elementGlue 要素の間に入る文字列
	 * @return string 変換後の文字列
	 * @static
	 */
	public static function toString ($value, $fieldGlue = '', $elementGlue = ',') {
		if (is_object($value)) {
			if ($value = $value->__toString()) {
				return $value;
			}
			$value = get_object_vars($value);
		} else if (!is_array($value)) {
			return $value;
		}

		$elements = array();
		foreach ($value as $key => $element) {
			$elements[] = $key . $fieldGlue . $element;
		}
		return implode($elementGlue, $elements);
	}
}

/* vim:set tabstop=4 ai: */
?>