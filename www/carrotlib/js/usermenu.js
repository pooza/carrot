/**
 * ユーザーメニュー
 *
 * 設置例:
 * <div id="usermenu">
 *   <dl id="usermenu_sample1">
 *     <dt>
 *       <img src="/carrotlib/images/spacer.gif" width="60" height="25" alt="HOME" />
 *     </dt>
 *     <dd>
 *       <ul>
 *         <li><a href="http://www.yahoo.co.jp/">Yahoo!</a></li>
 *         <li><a href="http://www.google.co.jp/">Google</a></li>
 *       </ul>
 *     </dd>
 *   </dl>
 *   <dl id="usermenu_sample2">
 *     <dt>
 *       <img src="/carrotlib/images/spacer.gif" width="60" height="25" alt="DIARY" />
 *     </dt>
 *   </dl>
 * </div>
 * <script type="text/javascript">
 * actions.onload.push(function () {
 *   new UserMenu('sample1'); //#usermenu_sample1に対応
 *   new UserMenu('sample2'); //#usermenu_sample2に対応
 * });
 * </script>
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @see http://www.leigeber.com/2008/04/sliding-javascript-dropdown-menu/ 改造もと
 */

function UserMenu (id) {
  var imagePath = '/carrotlib/images/usermenu/';
  var selectorPrefix = 'usermenu';
  var speed = 10;
  var timer = 15;
  var opacity = 0.9;

  var selector = '#' + selectorPrefix + '_' + id;
  var tab = $$(selector + ' dt')[0];
  var items = $$(selector + ' dd')[0];
  var tabImage = $$(selector + ' dt img')[0];

  if (items) {
    tab.onmouseover = function () {setMenuStatus(true)};
    tab.onmouseout = function () {setMenuStatus(false)};
    items.onmouseover = function () {cancelHide()};
    items.onmouseout = function () {setMenuStatus(false)};
  } else {
    tab.onmouseover = function () {setTabStatus(true)};
    tab.onmouseout = function () {setTabStatus(false)};
  }
  setMenuStatus(false);

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
    } else {
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