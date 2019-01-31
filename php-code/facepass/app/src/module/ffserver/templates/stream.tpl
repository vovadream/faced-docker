<Feed feed<?= $id ?>.ffm>
    File /tmp/feed<?= $id ?>.ffm
    FileMaxSize 200M
    Launch ffmpeg -rtsp_transport tcp -i "<?= $rtsp ?>"
</Feed>

<Stream s<?= $id ?>.mjpeg>
    Feed feed<?= $id ?>.ffm
    Format mpjpeg
    VideoBitRate 3000
    VideoFrameRate 15
    VideoSize 600x400
    VideoIntraOnly
    NoAudio
    Strict -1
    NoDefaults
</Stream>
