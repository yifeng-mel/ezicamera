pkill webrtc-sendrecv
pkill gst-launch-1.0
export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usr/lib
# record video to 10 minutes interval parameter is in nano seconds
cd /videos && gst-launch-1.0 -e v4l2src device=/dev/video0 ! video/x-raw, width=640, height=480, framerate=15/1 ! clockoverlay time-format="%D %H:%M:%S" ! videoconvert ! queue ! vp8enc deadline=1 ! tee name=t ! queue ! splitmuxsink muxer=avimux location=`date '+%Y-%m-%d-%H-%M-%S'`-%02d.avi max-size-time=1200000000000