TOKEN=$1
pkill gst-launch-1.0
pkill webrtc-sendrecv
/webrtc_app/webrtc-sendrecv --peer-id=$TOKEN --server=127.0.0.1:8443
