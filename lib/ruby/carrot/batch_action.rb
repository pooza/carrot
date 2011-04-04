#!/usr/bin/env ruby

# バッチ処理
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>

require 'carrot/constants'

class BatchAction < Array
  def register (m, a)
    self.push({:m => m, :a => a})
  end

  def execute
    self.each do |action|
      cmd = [
        Constants.new['BS_SUDO_DIR'] + '/bin/sudo',
        '-u',
        Constants.new['BS_APP_PROCESS_UID'],
        ROOT_DIR + '/bin/carrotctl.php',
      ]
      action.each do |key, value|
        cmd.push('-' + key.to_s)
        cmd.push(value)
      end
      system(cmd.join(' '))
    end
  end
end
