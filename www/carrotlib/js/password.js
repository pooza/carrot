/**
 * パスワード生成 JavaScript
 *
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */

function getRandomPassword (length) {
  src = 'ABCDEFGHJKLMNPRSTUVWXYZ2345678';
  password = '';
  for (i = 0 ; i < length ; i ++) {
    index = rand(src.length) - 1;
    password += src.slice(index, index + 1);
  }
  return password;
}

// The Central Randomizer 1.3 (C) 1997 by Paul Houle (houle@msc.cornell.edu)
// See: http://www.msc.cornell.edu/~houle/javascript/randomizer.html

rnd.today=new Date();
rnd.seed=rnd.today.getTime();

function rnd() {
rnd.seed = (rnd.seed*9301+49297) % 233280;
return rnd.seed/(233280.0);
};

function rand(number) {
return Math.ceil(rnd()*number);
};

// end central randomizer. -->
