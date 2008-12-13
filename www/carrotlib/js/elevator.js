/**
 * エレベータ処理
 *
 * 要 prototype.js
 *
 * example:
 * var elevator = new Elevator('div_id', 200, 20, 10);
 * new PeriodicalExecuter(function() {elevator.move()}, 0.1);
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */

function Elevator (id, x, yMin, yMargin) {
  this.element = $(id);
  this.x = x;
  this.yMin = yMin;
  this.yMargin = yMargin;

  this.move = function () {
    if (navigator.userAgent.match(/MSIE/)){
      var y = (document.body.scrollTop || document.documentElement.scrollTop);
    } else  {
      var y = self.pageYOffset;
    }
    if (y < this.yMin) {
      y = this.yMin;
    } else {
      y = y + this.yMargin;
    }

    this.element.style.position = 'absolute';
    this.element.style.left = x + 'px';
    this.element.style.top = y + 'px';
  }

  this.getElement = function () {
    return this.element;
  }
}
