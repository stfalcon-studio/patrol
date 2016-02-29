#!/usr/bin/env ruby

require_relative 'lib/videoconvert_3gp.rb'
require_relative 'lib/videoconvert_avi.rb'

mysql_opts = { host: 'localhost', username: 'someuser', password: 'somepass', database: 'somepdb' }
base_path = '/var/www/example.com/web/uploads/violation_videos/'
VideoConvert.new(mysql_opts, base_path)
VideoConvertAvi.new(mysql_opts, base_path)
