/**
 * carrot汎用 JavaScript
 *
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: carrot.js 365 2007-07-24 15:55:31Z pooza $
 */

function redirect (m, a, id) {
  url = '/?m=' + m + '&a=' + a;
  if (id) {
    url += '&id=' + id;
  }
  window.location.href = url;
}

function confirmDelete (m, a, recordType, id) {
  if (confirm('この' + recordType + 'を削除しますか？')) {
    redirect(m, a, id);
  }
}
