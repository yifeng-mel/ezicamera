FROM ubuntu:eoan

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update

RUN apt-get install -y --no-install-recommends \
    autoconf \
    automake \
    bison \
    build-essential \
    ca-certificates \
    flex \
    gettext \
    git \
    gnutls-dev \
    gtk-doc-tools \
    libffi-dev \
    libglib2.0 \
    libnice-dev \
    libopus-dev \
    libpango1.0-dev \
    libpcre3-dev \
    libsrtp2-dev \
    libusrsctp-dev \
    libssl-dev \
    libtool \
    libvpx-dev \
    libx264-dev \
    mount \
    perl \
    python \
    wget \
    zlib1g \
    libgirepository1.0-dev \
    python3-pip \
    libglib2.0-dev \
    libsoup2.4-dev \
    libjson-glib-dev \
    python3-setuptools

RUN cd / \
    && wget https://gstreamer.freedesktop.org/src/gstreamer/gstreamer-1.16.2.tar.xz \
    && tar xvfJ gstreamer-1.16.2.tar.xz > /dev/null \
    && cd gstreamer-1.16.2 \
    && ./configure --prefix=/usr \
    && make ERROR_CFLAGS="" \
    && make install

RUN cd / \
    && wget https://gstreamer.freedesktop.org/src/gst-plugins-base/gst-plugins-base-1.16.2.tar.xz \
    && tar xvfJ gst-plugins-base-1.16.2.tar.xz > /dev/null \
    && cd gst-plugins-base-1.16.2 \
    && ./configure --prefix=/usr \
    && make ERROR_CFLAGS="" \
    && make install

RUN cd / \
    && git clone https://github.com/libnice/libnice.git \
    && cd libnice \
    && git fetch origin latest-release \
    && git checkout latest-release \
    && ./autogen.sh --enable-compile-warnings=no --prefix=/usr --with-gstreamer --enable-static --enable-static-plugins --enable-shared --without-gstreamer-0.10 --disable-gtk-doc \
    && make install

RUN cd / \
    && wget https://gstreamer.freedesktop.org/src/gst-plugins-good/gst-plugins-good-1.16.2.tar.xz \
    && tar xvfJ gst-plugins-good-1.16.2.tar.xz > /dev/null \
    && cd gst-plugins-good-1.16.2 \
    && ./configure --prefix=/usr \
    && make ERROR_CFLAGS="" \
    && make install

RUN cd / \
    && wget https://gstreamer.freedesktop.org/src/gst-plugins-bad/gst-plugins-bad-1.16.2.tar.xz \
    && tar xvfJ gst-plugins-bad-1.16.2.tar.xz > /dev/null \
    && cd gst-plugins-bad-1.16.2 \
    && ./configure --prefix=/usr --enable-introspection \
    && make ERROR_CFLAGS="" \
    && make install

RUN cd / \
    && wget https://gstreamer.freedesktop.org/src/gst-plugins-ugly/gst-plugins-ugly-1.16.2.tar.xz \
    && tar xvfJ gst-plugins-ugly-1.16.2.tar.xz > /dev/null \
    && cd gst-plugins-ugly-1.16.2 \
    && ./configure --prefix=/usr \
    && make ERROR_CFLAGS="" \
    && make install 

RUN cd / \
    && wget https://gstreamer.freedesktop.org/src/gst-rtsp-server/gst-rtsp-server-1.16.2.tar.xz \
    && tar xvfJ gst-rtsp-server-1.16.2.tar.xz > /dev/null \
    && cd gst-rtsp-server-1.16.2 \
    && ./configure --prefix=/usr \
    && make ERROR_CFLAGS="" \
    && make install

RUN cd / \
    && pip3 install websockets