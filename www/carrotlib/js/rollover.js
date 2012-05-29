/**
 * ロールオーバー
 *
 * @package org.carrot-framework
 * @link http://d.hatena.ne.jp/kazeburo/20051227/p1
 */

RollOverImage = Class.create({
  initialize: function (img) {
    this.image = $(img);
    this.originalPath = this.image.src;
    if (arguments[1]) {
      this.setMouseOverImage(arguments[1]);
    }
    if (arguments[2]) {
      this.setMouseDownImage(arguments[2]);
    }
  },
  setMouseOverImage: function (path) {
    this.mouseOverImage = new Image();
    this.mouseOverImage.src = path;
    this.image.onmouseover = this.rollover.bind(this);
    this.image.onmouseout = this.reversion.bind(this);
  },
  setMouseDownImage: function (path) {
    this.mouseDownImage = new Image();
    this.mouseDownImage.src = path;
    this.image.onmousedown = this.mousedown.bind(this);
    this.image.onmouseup = this.reversion.bind(this);
  },
  rollover: function () {
    this.image.src = this.mouseOverImage.src;
  },
  mousedown: function () {
    this.image.src = this.mouseDownImage.src;
  },
  reversion: function () {
    this.image.src = this.originalPath;
  },

  initialized: true
});
