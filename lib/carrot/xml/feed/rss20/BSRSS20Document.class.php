<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.feed.rss20
 */

/**
 * RSS2.0文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSRSS20Document extends BSRSS09Document {
	protected $version = '2.0';

	/**
	 * 妥当な文書か？
	 *
	 * @access public
	 * @return boolean 妥当な文書ならTrue
	 */
	public function validate () {
		return (parent::validate()
			&& $this->query('/rss/channel/title')
			&& $this->query('/rss/channel/description')
			&& $this->query('/rss/channel/link')
		);
	}
}

/* vim:set tabstop=4: */
