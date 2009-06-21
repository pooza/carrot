/**
 * carrot汎用 JavaScript
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */

function redirect (m, a, id) {
  var url = '/' + m + '/' + a;
  if (id) {
    url += '/' + id;
  }
  window.location.href = url;
}

function confirmDelete (m, a, recordType, id) {
  if (confirm('この' + recordType + 'を削除しますか？')) {
    redirect(m, a, id);
  }
}

function openPictogramPallet (id) {
  window.open(
    '/UserPictogram/Choice?field=' + id,
    'pictogram',
    'width=240,height=300,scrollbars=yes'
  );
}

var actions = {};
actions['onload'] = [];

window.onload = function () {
  for (var i = 0 ; i < actions['onload'].length ; i ++) {
    actions['onload'][i]();
  }
}

actions['onload'].push(
  function () {
    try {
      AjaxZip2.JSONDATA = '/carrotlib/js/ajaxzip2/data';
    } catch (e) {
    }
  }
);