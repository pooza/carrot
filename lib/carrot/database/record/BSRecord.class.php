<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.record
 */

/**
 * テーブルのレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSRecord implements ArrayAccess,
	BSSerializable, BSAssignable, BSAttachmentContainer, BSImageContainer, BSHTTPRedirector {

	use BSHTTPRedirectorMethods, BSBasicObject, BSSerializableMethods;
	protected $attributes;
	protected $table;
	protected $url;
	protected $criteria;
	protected $records;
	protected $digest;
	const ACCESSOR = 'i';

	/**
	 * @access public
	 * @param BSTableHandler $table テーブルハンドラ
	 * @param string[] $attributes 属性の連想配列
	 */
	public function __construct (BSTableHandler $table, $attributes = null) {
		$this->table = $table;
		$this->attributes = BSArray::create();
		$this->records = BSArray::create();
		if ($attributes) {
			$this->initialize($attributes);
		}
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (mb_ereg('^get([[:upper:]][[:alnum:]]+)$', $method, $matches)) {
			$name = $matches[1];
			if (!$this->records->hasParameter($name)) {
				$table = BSTableHandler::create($name);
				$this->records[$name] = $table->getRecord($this[$table->getName() . '_id']);
			}
			return $this->records[$name];
		}

		$message = new BSStringFormat('仮想メソッド"%s"は未定義です。');
		$message[] = $method;
		throw new BadFunctionCallException($message);
	}

	/**
	 * 属性値を初期化
	 *
	 * @access public
	 * @param string[] $attributes 属性の連想配列
	 * @return BSRecord 自分自身
	 */
	public function initialize ($attributes) {
		$this->attributes->clear();
		$this->attributes->setParameters($attributes);
		return $this;
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return string 属性値
	 */
	public function getAttribute ($name) {
		return $this->attributes[BSString::toLower($name)];
	}

	/**
	 * 全属性を返す
	 *
	 * @access public
	 * @return BSArray 全属性値
	 */
	public function getAttributes () {
		return clone $this->attributes;
	}

	/**
	 * 抽出条件を返す
	 *
	 * @access protected
	 * @return BSCriteriaSet 抽出条件
	 */
	protected function getCriteria () {
		if (!$this->criteria) {
			$this->criteria = $this->createCriteriaSet();
			$this->criteria->register($this->getTable()->getKeyField(), $this);
		}
		return $this->criteria;
	}

	/**
	 * 更新
	 *
	 * @access public
	 * @param mixed $values 更新する値
	 * @param integer $flags フラグのビット列
	 *   BSDatabase::WITHOUT_LOGGING ログを残さない
	 *   BSDatabase::WITHOUT_SERIALIZE シリアライズしない
	 */
	public function update ($values, $flags = 0) {
		if (!$this->isUpdatable()) {
			throw new BSDatabaseException($this . 'を更新することはできません。');
		}

		$values = BSArray::create($values);
		$db = $this->getDatabase();
		$table = $this->getTable();
		$fields = $table->getProfile()->getFields();
		$key = $table->getUpdateDateField();
		if (!$values->hasParameter($key) && $fields[$key]) {
			$values[$key] = BSDate::getNow('Y-m-d H:i:s');
		}
		if (!$values->count()) {
			return;
		}

		$db->exec(BSSQL::getUpdateQueryString($table, $values, $this->getCriteria(), $db));
		$this->attributes->setParameters($values);
		if (($record = $this->getParent()) && !($flags & BSDatabase::WITHOUT_PARENT)) {
			$record->touch();
		}
		if ($this->isSerializable() && !($flags & BSDatabase::WITHOUT_SERIALIZE)) {
			$this->removeSerialized();
		}
		if (!($flags & BSDatabase::WITHOUT_LOGGING)) {
			$this->getDatabase()->log($this . 'を更新しました。');
		}
	}

	/**
	 * 更新可能か？
	 *
	 * @access protected
	 * @return boolean 更新可能ならTrue
	 */
	protected function isUpdatable () {
		return false;
	}

	/**
	 * 更新日付のみ更新
	 *
	 * @access public
	 */
	public function touch () {
		$this->update([], BSDatabase::WITHOUT_LOGGING);
	}

	/**
	 * 削除
	 *
	 * @access public
	 * @param integer $flags フラグのビット列
	 *   BSDatabase::WITHOUT_LOGGING ログを残さない
	 */
	public function delete ($flags = null) {
		if (!$this->isDeletable()) {
			throw new BSDatabaseException($this . 'を削除することはできません。');
		}

		if (!$this->getDatabase()->hasForeignKey()) {
			foreach ($this->getTable()->getChildClasses() as $class) {
				$table = BSTableHandler::create($class);
				$table->getCriteria()->register($this->getTable()->getName() . '_id', $this);
				foreach ($table as $record) {
					$record->delete();
				}
			}
		}
		if (($record = $this->getParent()) && !($flags & BSDatabase::WITHOUT_PARENT)) {
			$record->touch();
		}
		$this->getDatabase()->exec(
			BSSQL::getDeleteQueryString($this->getTable(), $this->getCriteria())
		);
		foreach ($this->getTable()->getImageNames() as $field) {
			$this->removeImageFile($field);
		}
		foreach ($this->getTable()->getAttachmentNames() as $field) {
			$this->removeAttachment($field);
		}
		$this->removeSerialized();
		if (!($flags & BSDatabase::WITHOUT_LOGGING)) {
			$this->getDatabase()->log($this . 'を削除しました。');
		}
	}

	/**
	 * 削除可能か？
	 *
	 * @access protected
	 * @return boolean 削除可能ならTrue
	 */
	protected function isDeletable () {
		return false;
	}

	/**
	 * 表示して良いか？
	 *
	 * @access public
	 * @return boolean 表示して良いならTrue
	 */
	public function isVisible () {
		return ($this['status'] == 'show');
	}

	/**
	 * 生成元テーブルハンドラを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブルハンドラ
	 */
	public function getTable () {
		return $this->table;
	}

	/**
	 * データベースを返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		return $this->getTable()->getDatabase();
	}

	/**
	 * 親レコードを返す
	 *
	 * 適切にオーバーライドすれば、update等の動作が少し利口に。
	 *
	 * @access public
	 * @return BSRecord 親レコード
	 */
	public function getParent () {
		return null;
	}

	/**
	 * 抽出条件を生成して返す
	 *
	 * @access protected
	 * @return BSCriteriaSet 抽出条件
	 */
	protected function createCriteriaSet () {
		return $this->getDatabase()->createCriteriaSet();
	}

	/**
	 * IDを返す
	 *
	 * @access public
	 * @return integer ID
	 */
	public function getID () {
		return $this[$this->getTable()->getKeyField()];
	}

	/**
	 * 更新日を返す
	 *
	 * @access public
	 * @return BSDate 更新日
	 */
	public function getUpdateDate () {
		return BSDate::create($this[$this->getTable()->getUpdateDateField()]);
	}

	/**
	 * 作成日を返す
	 *
	 * @access public
	 * @return BSDate 作成日
	 */
	public function getCreateDate () {
		return BSDate::create($this[$this->getTable()->getCreateDateField()]);
	}

	/**
	 * メールを送信
	 *
	 * @access public
	 * @param string $template テンプレート名
	 * @param BSParameterHolder $params アサインするパラメータ
	 */
	public function sendMail ($template, BSParameterHolder $params = null) {
		try {
			$mail = new BSSmartyMail;
			$mail->getRenderer()->setTemplate(get_class($this) . '.' . $template . '.mail');
			$mail->getRenderer()->setAttribute(BSString::underscorize(get_class($this)), $this);
			$mail->getRenderer()->setAttribute('params', $params);
			$mail->send();
			unset($mail);
		} catch (Exception $e) {
			throw new BSMailException('メールの送信に失敗しました。', $e->getCode(), $e);
		}
	}

	/**
	 * 添付ファイルの情報を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string[] 添付ファイルの情報
	 */
	public function getAttachmentInfo ($name) {
		if ($file = $this->getAttachment($name)) {
			$info = BSArray::create();
			if ($file instanceof BSAssignable) {
				$info->setParameters($file->assign());
			}
			$info['path'] = $file->getPath();
			$info['size'] = $file->getSize();
			$info['type'] = $file->getType();
			$info['filename'] = $this->getAttachmentFileName($name);
			return $info;
		}
	}

	/**
	 * 添付ファイルを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSFile 添付ファイル
	 */
	public function getAttachment ($name) {
		$finder = new BSFileFinder;
		$finder->clearDirectories();
		$finder->registerDirectory($this->getTable()->getDirectory());
		$finder->registerSuffixes(BSMIMEType::getInstance()->getAllSuffixes());
		return $finder->execute($this->getAttachmentBaseName($name));
	}

	/**
	 * 添付ファイルを設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param BSFile $file 添付ファイル
	 * @param string $filename ファイル名
	 */
	public function setAttachment ($name, BSFile $file, $filename = null) {
		if ($file instanceof BSImageFile) {
			$this->removeImageFile($name);
			$file->rename($this->getImageFileBaseName($name));
		} else {
			$this->removeAttachment($name);
			if (BSString::isBlank($suffix = $file->getSuffix())) {
				if (BSString::isBlank($filename)) {
					$file->setBinary(true);
					$suffix = BSMIMEType::getSuffix($file->analyzeType());
				} else {
					$suffix = BSFileUtility::getSuffix($filename);
				}
			}
			$file->rename($this->getAttachmentBaseName($name) . $suffix);
		}
		$file->moveTo($this->getTable()->getDirectory());

		$message = new BSStringFormat('%sの%sを設定しました。');
		$message[] = $this;
		$message[] = BSTranslateManager::getInstance()->translate($name);
		$this->getDatabase()->log($message);
	}

	/**
	 * 添付ファイルを削除する
	 *
	 * @access public
	 * @param string $name 名前
	 */
	public function removeAttachment ($name) {
		if ($file = $this->getAttachment($name)) {
			$file->delete();
			$message = new BSStringFormat('%sの%sを削除しました。');
			$message[] = $this;
			$message[] = BSTranslateManager::getInstance()->translate($name);
			$this->getDatabase()->log($message);
		}
	}

	/**
	 * 添付ファイルベース名を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string 添付ファイルベース名
	 */
	public function getAttachmentBaseName ($name) {
		return sprintf('%06d_%s', $this->getID(), $name);
	}

	/**
	 * 添付ファイルのダウンロード時の名を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string ダウンロード時ファイル名
	 */
	public function getAttachmentFileName ($name) {
		if ($file = $this->getAttachment($name)) {
			return $this->getAttachmentBaseName($name) . $file->getSuffix();
		}
	}

	/**
	 * 添付ファイルをまとめて設定
	 *
	 * @access public
	 * @param BSWebRequest $request リクエスト
	 */
	public function setAttachments (BSWebRequest $request) {
		$dir = $request->getSession()->getDirectory();
		foreach ($this->getTable()->getImageNames() as $name) {
			if ($info = $request[$name]) {
				$this->setImageFile($name, new BSImageFile($info['tmp_name']));
			} else {
				foreach (BSImage::getSuffixes() as $suffix) {
					if ($file = $dir->getEntry($name . $suffix, 'BSImageFile')) {
						$this->setImageFile($name, $file);
						break;
					}
				}
			}
		}
		foreach ($this->getTable()->getAttachmentNames() as $name) {
			if ($info = $request[$name]) {
				$this->setAttachment($name, new BSFile($info['tmp_name']), $info['name']);
			}
		}
	}

	/**
	 * キャッシュをクリア
	 *
	 * @access public
	 * @param string $size
	 */
	public function removeImageCache ($size) {
		(new BSImageManager)->removeThumbnail($this, $size);
	}

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセルサイズ
	 * @param integer $flags フラグのビット列
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo ($size, $pixel = null, $flags = 0) {
		return (new BSImageManager)->getImageInfo($this, $size, $pixel, $flags);
	}

	/**
	 * 画像ファイルを返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return BSImageFile 画像ファイル
	 */
	public function getImageFile ($size) {
		foreach (BSImage::getSuffixes() as $suffix) {
			$name = $this->getImageFileBaseName($size) . $suffix;
			if ($file = $this->getTable()->getDirectory()->getEntry($name, 'BSImageFile')) {
				return $file;
			}
		}
	}

	/**
	 * 画像ファイルを設定
	 *
	 * @access public
	 * @param string $size 画像名
	 * @param BSImageFile $file 画像ファイル
	 */
	public function setImageFile ($size, BSImageFile $file) {
		$this->setAttachment($size, $file);
	}

	/**
	 * 画像ファイルを削除する
	 *
	 * @access public
	 * @param string $size サイズ名
	 */
	public function removeImageFile ($size) {
		$this->removeImageCache($size);
		$this->removeAttachment($size);
	}

	/**
	 * 画像ファイルベース名を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return string 画像ファイルベース名
	 */
	public function getImageFileBaseName ($size) {
		return sprintf('%06d_%s', $this->getID(), $size);
	}

	/**
	 * ラベルを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		foreach (['name', 'label', 'title'] as $name) {
			foreach ([null, $this->getTable()->getName() . '_'] as $prefix) {
				foreach ([null, '_' . $language] as $suffix) {
					if (!BSString::isBlank($label = $this[$prefix . $name . $suffix])) {
						return $label;
					}
				}
			}
		}
	}

	/**
	 * ラベルを返す
	 *
	 * getLabelのエイリアス
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 * @final
	 */
	final public function getName ($language = 'ja') {
		return $this->getLabel($language);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->attributes->hasParameter($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BSDatabaseException('レコードの属性を直接更新することはできません。');
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BSDatabaseException('レコードの属性は削除できません。');
	}

	/**
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		if (!$this->url) {
			if (BSString::isBlank($this['url'])) {
				$this->url = BSURL::create(null, 'carrot');
				$this->url['module'] = 'User' . BSString::pascalize($this->getTable()->getName());
				$this->url['action'] = 'Detail';
				$this->url['record'] = $this;
			} else {
				$this->url = BSURL::create($this['url']);
			}
		}
		return $this->url;
	}

	/**
	 * シリアライズするか？
	 *
	 * @access public
	 * @return boolean シリアライズするならTrue
	 */
	public function isSerializable () {
		return BSSerializeHandler::getClasses()->isContain(get_class($this));
	}

	/**
	 * ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		if (!$this->digest) {
			$this->digest = BSCrypt::digest([
				get_class($this),
				$this->getID(),
			]);
		}
		return $this->digest;
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		if (!$this->isSerializable()) {
			throw new BSDatabaseException($this . 'はシリアライズできません。');
		}
		$this->controller->setAttribute($this, $this->getSerializableValues());
	}

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		if ($date = $this->getUpdateDate()) {
			return $this->controller->getAttribute($this, $date);
		} else {
			return $this->controller->getAttribute($this);
		}
	}

	/**
	 * 全てのファイル属性
	 *
	 * @access protected
	 * @return BSArray ファイル属性の配列
	 */
	protected function getSerializableValues () {
		$values = $this->getAttributes();
		if ($url = $this->getURL()) {
			$values['url'] = $url->getContents();
		}
		foreach ($this->getTable()->getImageNames() as $field) {
			if (!!$this->getImageFile($field)) {
				$values['has_' . $field] = true;
				$values[$field] = $this->getImageInfo($field);
			}
		}
		foreach ($this->getTable()->getAttachmentNames() as $field) {
			if (!!$this->getAttachment($field)) {
				$values['has_' . $field] = true;
				$values[$field] = $this->getAttachmentInfo($field);
			}
		}
		return $values;
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return BSArray アサインすべき値
	 */
	public function assign () {
		if ($this->isSerializable()) {
			if (BSString::isBlank($this->getSerialized())) {
				$this->serialize();
			}
			$values = $this->getSerialized();
		} else {
			$values = $this->getSerializableValues();
		}
		$values['is_visible'] = $this->isVisible();
		return $values;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		try {
			$word = BSTranslateManager::getInstance()->execute($this->getTable()->getName());
		} catch (BSTranslateException $e) {
			$word = $this->getTable()->getName();
		}
		return sprintf('%s(%s)', $word, $this->getID());
	}
}

