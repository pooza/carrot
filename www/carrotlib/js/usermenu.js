/**
 * ユーザーメニュー
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @see http://www.leigeber.com/2008/04/sliding-javascript-dropdown-menu/ 改造もと
 */

function UserMenu (id) {
  var imagePath = '/carrotlib/images/usermenu/';
  var speed = 10;
  var timer = 15;
  var opacity = 0.9;

  var tab = $('menu_tab_' + id);
  var items = $('menu_items_' + id);
  var tabImage = $('menu_tab_image_' + id);

  if (items) {
    tab.onmouseover = function () {setMenuStatus(true)};
    tab.onmouseout = function () {setMenuStatus(false)};
    items.onmouseover = function () {cancelHide()};
    items.onmouseout = function () {setMenuStatus(false)};
  } else {
    tab.onmouseover = function () {setTabStatus(true)};
    tab.onmouseout = function () {setTabStatus(false)};
  }


  function setTabStatus (flag) {
    if (flag) {
      tabImage.src = imagePath + id + '_on.gif';
    } else {
      tabImage.src = imagePath + id + '.gif';
    }
  }

  function setMenuStatus (flag) {
    setTabStatus(flag);
    clearInterval(items.timer);
    if (flag) {
      clearTimeout(tab.timer);
      if (items.maxHeight && items.maxHeight <= items.offsetHeight) {
        return;
      } else if (!items.maxHeight) {
        items.style.display = 'block';
        items.style.height = 'auto';
        items.maxHeight = items.offsetHeight;
        items.style.height = '0px';
      }
      items.timer = setInterval(function(){slide(true)}, timer);
    }else{
      tab.timer = setTimeout(collapse, 50);
    }
  }

  function collapse () {
    items.timer = setInterval(function(){slide(false)}, timer);
  }

  function cancelHide () {
    setTabStatus(true);
    clearTimeout(tab.timer);
    clearInterval(items.timer);
    if (items.offsetHeight < items.maxHeight) {
      items.timer = setInterval(function(){slide(true)}, timer);
    }
  }

  function slide (flag) {
    var y = items.offsetHeight;
    if (flag) {
      items.style.height = y + Math.max(1, Math.round((items.maxHeight - y) / speed)) + 'px';
    } else {
      items.style.height = y + (Math.round(y / speed) * -1) + 'px';
    }
    items.style.opacity = y / items.maxHeight * opacity;
    items.style.filter = 'alpha(opacity=' + (items.style.opacity * 100) + ')';
    if((y < 2 && !flag) || ((items.maxHeight - 2) < y && flag)){
      clearInterval(items.timer);
    }
  }
}