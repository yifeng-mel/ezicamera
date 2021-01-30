#!/bin/bash

if ! pgrep -x "gst-launch-1.0" > /dev/null
then
    if ! pgrep -x "webrtc-sendrecv" > /dev/null
    then
        /bin/bash /scripts/start_recording.bash
    fi
fi
# * * * * * /bin/bash /cronjob/start_recording_cronjob.bash
# * * * * * ( sleep 3; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 6; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 9; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 12; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 15; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 18; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 21; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 24; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 27; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 30; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 33; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 36; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 39; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 42; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 45; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 48; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 51; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 54; /bin/bash /cronjob/start_recording_cronjob.bash )
# * * * * * ( sleep 57; /bin/bash /cronjob/start_recording_cronjob.bash )
