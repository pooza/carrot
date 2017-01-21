# サーバ環境
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>

module Carrot
  class Environment
    def self.name
      return File.basename(ROOT_DIR)
    end

    def self.file_path
      return "#{ROOT_DIR}/webapp/config/constant/#{self.name}.yaml"
    end

    def self.os
      return 'Debian' if File.exist?('/usr/bin/apt-get')
      return `uname`.chomp
    end
  end
end