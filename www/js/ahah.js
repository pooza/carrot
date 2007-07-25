/**
 * AHAHエンジン
 *
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */

function ahah (divID, url) {
  function setAHAHContents () {
    if (req.readyState < 4) {
      return;
    } else if (req.status == 200) {
      document.getElementById(divID).innerHTML = req.responseText;
    } else {
      document.getElementById(divID).innerHTML = 'AHAH Error: ' + req.statusText;
    }
  }

  if (window.XMLHttpRequest) {
    var req = new XMLHttpRequest();
  } else if (window.ActiveXObject) {
    var req = new ActiveXObject('Microsoft.XMLHTTP');
  } else {
    return false;
  }

  req.onreadystatechange = function() {
    setAHAHContents();
  };
  req.open('GET', url, true);
  req.send('');
}