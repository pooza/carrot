/**
 * 画像お持ち帰り禁止
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */

function denyTakeOut (selector_name) {
  var doNothing = function () {return false;}
  var configureElement = function (element) {
    if (!element.oncontextmenu) {
      element.oncontextmenu = doNothing;
      element.onselectstart = doNothing;
      element.onmousedown = doNothing;
    }
  }

  if (!selector_name) {
    selector_name = '.deny_take_out';
  }
  var elements = $$(selector_name);
  for (var i = 0 ; i < elements.length ; i ++) {
    configureElement(elements[i]);
  }
}
