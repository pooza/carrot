<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.tablereport
 */

/**
 * TableReport出力用PDF
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTableReportPDF extends BSFPDF {
	private $sectionNumber = 0;

	/**
	 * ヘッダの内容を返す
	 *
	 * @access public
	 * @return string ヘッダの内容
	 */
	protected function getHeaderContents () {
		return 'TableReport';
	}

	/**
	 * 見出しを出力
	 *
	 * @access public
	 * @param string $str 見出し
	 */
	function putHeading ($str) {
		$this->addPage();
		$this->setFont(self::GOTHIC_FONT, null, 20);
		$this->putLine('■ テーブル ' . $str);
		$this->sectionNumber = 0;
	}

	/**
	 * 小見出しを出力
	 *
	 * @access public
	 * @param string $str 小見出し
	 */
	function putSectionHeading ($str) {
		$this->sectionNumber ++;
		$this->setFont(self::GOTHIC_FONT, null, 12);
		$this->ln();
		$this->putLine($this->sectionNumber . '. ' . $str);
	}

	/**
	 * 属性一覧を出力
	 *
	 * @access public
	 * @param string[][] $attributes 属性
	 */
	function putAttributes ($attributes) {
		$this->putSectionHeading('基本情報');
		foreach ($attributes as $name => $value) {
			$this->setFont(self::MINCHO_FONT, null, 10);
			$this->cell(40, 6, $name, 1, 0, self::LEFT);
			$this->cell(110, 6, $value, 1, 0, self::LEFT);
			$this->ln();
		}
	}

	/**
	 * フィールド一覧を出力
	 *
	 * @access public
	 * @param string[][] $fields フィールド
	 */
	function putFields ($fields) {
		$this->putSectionHeading('フィールド');

		$this->setFont(self::MINCHO_FONT, null, 10);
		$this->cell(40, 6, 'フィールド名', 1, 0, self::CENTER);
		$this->cell(60, 6, '型', 1, 0, self::CENTER);
		$this->cell(10, 6, 'NULL', 1, 0, self::CENTER);
		$this->cell(40, 6, '既定値', 1, 0, self::CENTER);
		$this->cell(40, 6, 'その他制約', 1, 0, self::CENTER);
		$this->ln();

		foreach ($fields as $field) {
			$this->cell(40, 6, $field['name'], 1, 0, self::LEFT);
			$this->cell(60, 6, $field['type'], 1, 0, self::LEFT);

			if ($field['notnull']) {
				$this->cell(10, 6, null, 1, 0, self::LEFT);
			} else {
				$this->cell(10, 6, '可', 1, 0, self::LEFT);
			}

			$this->cell(40, 6, $field['default'], 1, 0, self::LEFT);
			$this->cell(40, 6, $field['extra'], 1, 0, self::LEFT);
			$this->ln();
		}
	}

	/**
	 * キー一覧を出力
	 *
	 * @access public
	 * @param string[][] $keys キー
	 */
	function putKeys ($keys) {
		$this->putSectionHeading('キー');

		$this->setFont(self::MINCHO_FONT, null, 10);
		$this->cell(40, 6, 'キー名', 1, 0, self::CENTER);
		$this->cell(60, 6, '対象フィールド名', 1, 0, self::CENTER);
		$this->cell(10, 6, '一意', 1, 0, self::CENTER);
		$this->ln();

		foreach ($keys as $key) {
			$this->cell(40, 6, $key['name'], 1, 0, self::LEFT);
			$this->cell(60, 6, implode(',', $key['fields']), 1, 0, self::LEFT);
			if ($key['unique']) {
				$this->cell(10, 6, 'YES', 1, 0, self::LEFT);
			} else {
				$this->cell(10, 6, '', 1, 0, self::LEFT);
			}
			$this->ln();
		}
	}
}

/* vim:set tabstop=4 ai: */
?>