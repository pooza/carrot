/**
 * エレベータDiv JavaScript
 *
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: elevator.js 358 2007-07-14 11:39:53Z pooza $
 */

setInterval('moveDiv()', 500);
var div = document.getElementById(divID);
div.style.position = 'absolute';
moveDiv();

function moveDiv () {
  div.style.left = x + 'px';
  div.style.top = getDivY() + 'px';
}

function getDivY () {
  if (navigator.userAgent.match(/MSIE/)){
    y = (document.body.scrollTop || document.documentElement.scrollTop);
  } else  {
    y = self.pageYOffset;
  }
  if (y < yMin) {
    return yMin;
  } else {
    return y + yMargin;
  }
}
