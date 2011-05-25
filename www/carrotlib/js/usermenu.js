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
 * document.observe('dom:loaded', function () {
 *   new UserMenu('sample1'); //#usermenu_sample1に対応
 *   new UserMenu('sample2'); //#usermenu_sample2に対応
 * });
 * </script>
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link http://www.leigeber.com/2008/04/sliding-javascript-dropdown-menu/ 改造もと
 */

function UserMenu (id, options) {
  this.imagePath = '/carrotlib/images/usermenu/';
  this.selectorPrefix = 'usermenu';
  this.speed = 10;
  this.timer = 15;
  this.opacity = 0.9;
  this.offImageSuffix = '.gif';
  this.onImageSuffix = '_on.gif';
  this.offImage = null;
  this.onImage = null;

  if (Prototype.Browser.IE) {
    this.speed /= 5; // IEは5倍速で
  }

  if (options) {
    for (var index in options) {
      this[index] = options[index];
    }
  }
  if (!this.onImage) {
    this.onImage = this.imagePath + id + this.onImageSuffix;
  }
  if (!this.offImage) {
    this.offImage = this.imagePath + id + this.offImageSuffix;
  }

  var menu = this;
  var selector = '#' + this.selectorPrefix + '_' + id;
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
    if (!tabImage) {
      return;
    }
    if (flag) {
      tabImage.src = menu.onImage;
    } else {
      tabImage.src = menu.offImage;
    }
  }

  function setMenuStatus (flag) {
    setTabStatus(flag);
    if (!items) {
      return;
    }
    clearInterval(items.timer);
    if (flag) {
      if (items.maxHeight && items.maxHeight <= items.offsetHeight) {
        return;
      } else if (!items.maxHeight) {
        items.style.display = 'block';
        items.style.height = 'auto';
        items.maxHeight = items.offsetHeight;
        items.style.height = '0px';
      }
    }
    items.timer = setInterval(function(){slide(flag)}, menu.timer);
  }

  function cancelHide () {
    setTabStatus(true);
    if (!items) {
      return;
    }
    clearInterval(items.timer);
    if (items.offsetHeight < items.maxHeight) {
      items.timer = setInterval(function(){slide(true)}, menu.timer);
    }
  }

  function slide (flag) {
    if (!items) {
      return;
    }
    var y = items.offsetHeight;
    if (flag) {
      items.style.height = y + Math.max(1, Math.round((items.maxHeight - y) / menu.speed)) + 'px';
    } else {
      items.style.height = y + (Math.round(y / menu.speed) * -1) + 'px';
    }
    items.style.opacity = y / items.maxHeight * menu.opacity;
    items.style.filter = 'alpha(opacity=' + (items.style.opacity * 100) + ')';
    if ((y < 2 && !flag) || ((items.maxHeight - 2) < y && flag)) {
      clearInterval(items.timer);
    }
  }
}