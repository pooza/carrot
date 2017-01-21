# バッチ処理
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>

require 'carrot/constants'

module Carrot
  class BatchAction < Array
    attr :silent, true

    def register (m, a)
      self.push({
        :m => m,
        :a => a,
      })
    end

    def execute
      self.each do |action|
        cmd = [
          "#{Carrot::Constants.new['BS_SUDO_DIR']}/bin/sudo",
          '-u',
          Carrot::Constants.new['BS_APP_PROCESS_UID'],
          "#{Carrot::Constants.new['BS_PHP_DIR']}/bin/php",
          "#{ROOT_DIR}/bin/carrotctl.php",
        ]
        action.each do |key, value|
          cmd.push("-#{key.to_s}")
          cmd.push(value)
        end
        puts "== module:#{action[:m]} action:#{action[:a]}" unless @silent
        system(*cmd)
      end
    end
  end
end
