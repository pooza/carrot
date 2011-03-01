#!/usr/bin/env ruby

# サーバ環境
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>

class Environment
  def Environment.name
    return File.basename(ROOT_DIR)
  end

  def Environment.file_path
    return ROOT_DIR + '/webapp/config/constant/' + Environment.name + '.yaml'
  end

  def Environment.os
    return `uname`.chomp
  end
end
