/**
 * パスワード生成 JavaScript
 *
 * 要、rand.js
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */

function getRandomPassword (length) {
  var src = 'ABCDEFGHJKLMNPRSTUVWXYZ2345678';
  var password = '';
  for (var i = 0 ; i < length ; i ++) {
    var index = rand(src.length) - 1;
    password += src.slice(index, index + 1);
  }
  return password;
}
