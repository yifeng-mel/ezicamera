TOKEN=$1
WS_SERVER=$2
export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usr/lib
pkill gst-launch-1.0
pkill webrtc-sendrecv
/webrtc_app/webrtc-sendrecv --peer-id=$TOKEN --server=$WS_SERVER &>> /log/webrtc-sendrecv.txt
