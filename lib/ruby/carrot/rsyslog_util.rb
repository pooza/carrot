# rsyslogユーティリティ
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>

require 'carrot/environment'

module Carrot
  class RsyslogUtil
    def self.create_config_file
      body = []
      body.push("$template #{self.template_name}, \"#{self.log_path}\"")
      body.push(":programname, isequal, \"#{self.program_name}\" -?#{self.template_name}")
      File.open(self.config_path, 'w') do |file|
        file.write(body.join("\n"))
      end
    end

    private
    def self.program_name
      return "carrot-#{Carrot::Environment.name}"
    end

    def self.template_name
      return "LogFile#{Carrot::Environment.name.gsub('.', '').capitalize}Path"
    end

    def self.log_path
      return File.join(ROOT_DIR, "/var/log/%$now%.log")
    end

    def self.config_path
      return "/usr/local/etc/rsyslog.d/#{self.program_name}.conf"
    end
  end
end
