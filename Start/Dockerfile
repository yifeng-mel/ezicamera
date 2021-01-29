FROM smartvision/app:laravel

COPY scripts /scripts

COPY webrtc_app_src /webrtc_app_src

COPY start.bash /

RUN mkdir /videos && chmod -R 777 /videos

RUN chmod -R 777 /scripts && chmod -R 777 /webrtc_app_src

RUN mkdir /webrtc_app

RUN gcc /webrtc_app_src/webrtc-sendrecv.c $(pkg-config --cflags --libs gstreamer-webrtc-1.0 gstreamer-sdp-1.0 libsoup-2.4 json-glib-1.0) -o /webrtc_app/webrtc-sendrecv

RUN gcc /webrtc_app_src/datachannel.c $(pkg-config --cflags --libs gstreamer-webrtc-1.0 gstreamer-sdp-1.0 libsoup-2.4 json-glib-1.0) -o /webrtc_app/datachannel

ENTRYPOINT [ "bash", "/start.bash" ]