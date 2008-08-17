-- ログデータベース初期化
--
-- @package org.carrot-framework
-- @author 小石達也 <tkoishi@b-shock.co.jp>
-- @version $Id$

CREATE TABLE log (
  id integer NOT NULL PRIMARY KEY,
  date datetime NOT NULL,
  remote_host varchar(128) NOT NULL,
  priority varchar(32) NOT NULL,
  message varchar(256)
);
