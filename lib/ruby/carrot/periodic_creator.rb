# periodic
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>

require 'carrot/environment'

module Carrot
  class PeriodicCreator < Hash
    def initialize
      self[:basename] = 'carrot'
      self[:period] = 'daily'
      self[:source] = nil
    end

    def create
      self[:source] = default_source unless self[:source]
      system('sudo', 'mkdir', '-p', File.dirname(dest))
      system('sudo', 'ln', '-s', self[:source], dest) unless File.exist?(dest)
    end

    private
    def default_source
      return File.join(ROOT_DIR, "bin/#{self[:basename]}-#{self[:period]}.rb")
    end

    def dest
      return "#{prefix}#{self[:basename]}-#{Carrot::Environment.name}"
    end

    def prefix
      case Carrot::Environment.os
      when 'FreeBSD', 'Darwin'
        return "/usr/local/etc/periodic/#{self[:period]}/900."
      when 'Debian'
        return "/etc/cron.#{self[:period]}/"
      end
    end
  end
end