FROM smartvision/app:laravel

ENV DEBIAN_FRONTEND=noninteractive

ENV TZ=Australia/Melbourne

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY start.bash /

COPY scripts /scripts

COPY cronjob /cronjob

COPY all-cron /etc/cron.d/all-cron

COPY webrtc_app_src /webrtc_app_src

COPY rsa_keys /rsa_keys

RUN chmod 0644 /etc/cron.d/all-cron

RUN crontab /etc/cron.d/all-cron

RUN mkdir /videos && chmod -R 777 /videos

RUN chmod -R 777 /scripts && chmod -R 777 /webrtc_app_src && chmod -R 777 /cronjob && chmod -R 777 /rsa_keys

RUN mkdir /webrtc_app

RUN gcc /webrtc_app_src/webrtc-sendrecv.c $(pkg-config --cflags --libs gstreamer-webrtc-1.0 gstreamer-sdp-1.0 libsoup-2.4 json-glib-1.0) -o /webrtc_app/webrtc-sendrecv

RUN gcc /webrtc_app_src/datachannel.c $(pkg-config --cflags --libs gstreamer-webrtc-1.0 gstreamer-sdp-1.0 libsoup-2.4 json-glib-1.0) -o /webrtc_app/datachannel

ENTRYPOINT [ "bash", "/start.bash" ]