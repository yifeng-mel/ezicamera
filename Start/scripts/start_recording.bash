pkill webrtc-sendrecv
pkill gst-launch-1.0
# record video to 20 minutes interval parameter is in nano seconds
cd /videos && gst-launch-1.0 -e v4l2src device=/dev/video0 ! video/x-raw,width=400,height=400,framerate=15/1 ! splitmuxsink muxer=avimux location=`date '+%Y-%m-%d-%H-%M-%S'`-%02d.avi max-size-time=1200000000000