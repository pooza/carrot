#!/usr/local/bin/ruby

GZIP_CMD = '/usr/bin/gzip'
LOG_DIR = '/var/log/httpd/'
DAYS = 1

puts nil
puts 'アクセスログを圧縮:'
expires_on = Time.now - (60 * 60 * 24 * DAYS)
Dir.glob("#{LOG_DIR}*/*/*/*.log").each do |f|
  if File.new(f).mtime < expires_on
    puts f
    system(GZIP_CMD, '-f', f)
  end
end
