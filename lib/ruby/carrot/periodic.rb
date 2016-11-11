# periodic
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>

require 'carrot/environment'

class Periodic
  def self.create (period, src, basename = 'carrot')
    case Environment.os
    when 'FreeBSD', 'Darwin'
      prefix = "/usr/local/etc/periodic/#{period}/900."
    when 'Debian'
      prefix = "/etc/cron.#{period}/"
    end
    dest = "#{prefix}#{basename}-#{Environment.name}"
    system('sudo', 'mkdir', '-p', File.dirname(dest))
    system('sudo', 'ln', '-s', src, dest) unless File.exist?(dest)
  end
end
