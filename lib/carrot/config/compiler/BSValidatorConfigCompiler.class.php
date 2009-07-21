<?php
/**
 * @package org.carrot-framework
 * @subpackage config.compiler
 */

/**
 * バリデータ設定コンパイラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSValidatorConfigCompiler extends BSConfigCompiler {
	private $methods;
	private $fields;
	private $validators;

	public function execute (BSConfigFile $file) {
		$this->clearBody();
		$this->parse($file);

		$this->putLine('$manager = BSValidateManager::getInstance();');
		$this->putLine('$request = BSRequest::getInstance();');
		$line = new BSStringFormat('if (in_array($request->getMethod(), %s)) {');
		$line[] = self::quote($this->methods->getParameters());
		$this->putLine($line);
		foreach ($this->fields as $name => $validators) {
			foreach ($validators as $validator) {
				$line = new BSStringFormat('  $manager->register(%s, new %s(%s));');
				$line[] = self::quote($name);
				$line[] = $this->validators[$validator]['class'];
				$line[] = self::quote((array)$this->validators[$validator]['params']);
				$this->putLine($line);
			}
		}
		$this->putLine('}');

		return $this->getBody();
	}

	private function parse (BSConfigFile $file) {
		$this->validators = new BSArray;
		require(BSConfigManager::getInstance()->compile('validator/carrot'));
		$this->validators->setParameters($config);
		require(BSConfigManager::getInstance()->compile('validator/application'));
		$this->validators->setParameters($config);

		$config = new BSArray($file->getResult());
		$this->parseMethods(new BSArray($config['methods']));
		$this->parseFields(new BSArray($config['fields']));
		$this->parseValidators(new BSArray($config['validators']));
	}

	private function parseMethods (BSArray $config) {
		if (!$config->count()) {
			$config[] = 'GET';
			$config[] = 'POST';
		}

		$this->methods = new BSArray;
		foreach ($config as $method) {
			$method = BSString::toUpper($method);
			if (!BSRequest::getMethods()->isContain($method)) {
				throw new BSConfigException('"%s"は正しくないメソッドです。', $method);
			}
			$this->methods[] = $method;
		}
	}

	private function parseFields (BSArray $config) {
		$this->fields = new BSArray;
		foreach ($config as $name => $field) {
			$field = new BSArray($field);

			$this->fields[$name] = new BSArray;
			if ($field['file']) {
				$this->fields[$name][] = 'file';
			} else {
				$this->fields[$name][] = 'string';
			}
			if ($field['required']) {
				$this->fields[$name][] = 'empty';
			}
			$this->fields[$name]->merge($field['validators']);
			$this->fields[$name]->uniquize();

			foreach ($this->fields[$name] as $validator) {
				if (!$this->validators[$validator]) {
					$this->validators[$validator] = null;
				}
			}
		}
	}

	private function parseValidators (BSArray $config) {
		$this->validators->setParameters($config);
		foreach ($this->validators as $name => $values) {
			if (!$values) {
				throw new BSConfigException('バリデータ "%s" が未定義です。', $name);
			}
			$this->validators[$name] = new BSArray($values);
		}
	}
}

/* vim:set tabstop=4: */
