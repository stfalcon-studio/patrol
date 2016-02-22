class VideoConvertAvi < VideoConvert
  def lock
    if File.file?('/tmp/video_converting_avi.lock')
      exit
    else
      FileUtils.touch('/tmp/video_converting_avi.lock')
    end
  end

  def unlock
    FileUtils.rm('/tmp/video_converting_avi.lock')
  end

  def list_of_videos
    @client.query("SELECT `id`,`VideoFilename` FROM `violations` WHERE `VideoFilename` LIKE '%.avi%' AND `status` = 'waiting';")
  end

  def format
    '.avi'
  end

end
