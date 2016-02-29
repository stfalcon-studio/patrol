class VideoConvert
  def initialize(mysql_opts, base_path)
    require 'mysql2'
    require 'fileutils'
    @client = Mysql2::Client.new(mysql_opts)
    lock
    list_of_videos.each do |video|
      update_video_status(video['id'], 'converting')
      convert(base_path + video['VideoFilename'], base_path + video['VideoFilename'].split(format)[0] + '.mp4')
      update_video_path(video['id'], video['VideoFilename'].split(format)[0] + '.mp4')
      update_video_status(video['id'], 'ready')
    end
    unlock
  end

  def convert(src_path, dst_path)
    pid = Process.spawn("ffmpeg -i #{src_path} -qscale 0 -ab 64k -ar 44100 #{dst_path}")
    Process.wait(pid)
  end

  private

  def lock
    if File.file?('/tmp/video_converting.lock')
      exit
    else
      FileUtils.touch('/tmp/video_converting.lock')
    end
  end

  def unlock
    FileUtils.rm('/tmp/video_converting.lock')
  end

  def list_of_videos
    @client.query("SELECT `id`,`VideoFilename` FROM `violations` WHERE `VideoFilename` LIKE '%.3gp%' AND `status` = 'waiting';")
  end

  def update_video_path(video_id, path)
    @client.query("UPDATE `violations` SET `VideoFilename` = '#{path}' WHERE `violations`.`id` = #{video_id};")
  end

  def update_video_status(video_id, status)
    @client.query("UPDATE `violations` SET `status` = '#{status}' WHERE `violations`.`id` = #{video_id};")
  end

  def format
    '.3gp'
  end

end
