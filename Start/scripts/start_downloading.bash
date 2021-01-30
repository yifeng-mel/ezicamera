TOKEN=$1
FILE_NAME=$2
WS_SERVER=$3
export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usr/lib
pkill datachannel
/webrtc_app/datachannel --peer-id=$TOKEN --file-name=$FILE_NAME --server=$WS_SERVER