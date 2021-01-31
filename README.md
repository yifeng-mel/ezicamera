# EziCamera
Do you want to make your Raspberry Pi a security camera?  This is the right place.

## Preparation
Following list is what you need to prepare before running the app: 
1. Raspberry Pi OS is the operating system. (https://www.raspberrypi.org/documentation/installation/installing-images/)
2. Camera is installed and enabled on your Raspberry Pi. (https://www.raspberrypi.org/documentation/configuration/camera.md)
3. Your Raspberry Pi has Internet access. (https://www.raspberrypi.org/documentation/configuration/wireless/wireless-cli.md)
4. Docker is installed on your Raspberry Pi. (https://phoenixnap.com/kb/docker-on-raspberry-pi)
5. Request a camera uid from cloud camera manager.

## Pull the Git Repository
```
cd ~ && git clone https://github.com/yifeng-mel/ezicamera.git
```

## Generate RSA Keys 
```
cd ~/ezicamera/Start/rsa_keys/ && ssh-keygen -f ./camera_id_rsa
```

## Build the Docker Image
```
cd ~/ezicamera/Start && sudo docker build . -t smartvision/app:start
```


## Create User and Set Camera Uid
```
sudo docker run \
    -it \
    --entrypoint /bin/bash \
    --network host \
    --device /dev/video0 \
    -v etcletsencrypt_vol:/etc/letsencrypt \
    -v varlibletsencrypt_vol:/var/lib/letsencrypt \
    -v apachesitesavailable_vol:/etc/apache2/sites-available \
    -v videos_vol:/videos \
    -v log_vol:/log \
    -v database_vol:/database \
    smartvision/app:start
    
(inside the docker container)
cd /web && \
    php artisan create:user {Your Email} {Your Password} && \
    php artisan setCameraUid {camera uid}

exit
```

## Run
```
sudo docker run \
    --network host \
    --device /dev/video0 \
    -v etcletsencrypt_vol:/etc/letsencrypt \
    -v varlibletsencrypt_vol:/var/lib/letsencrypt \
    -v apachesitesavailable_vol:/etc/apache2/sites-available \
    -v videos_vol:/videos \
    -v database_vol:/database \
    -v log_vol:/log \
    smartvision/app:start \
    smartvision.mel@gmail.com \
    cam1.ezicamera.com \
    {ID}.cam1.ezicamera.com
    
```
To run the docker image in the interactive mode and start the application in the docker container:
```
sudo docker run \
    -it \
    --entrypoint /bin/bash \
    --network host \
    --device /dev/video0 \
    -v etcletsencrypt_vol:/etc/letsencrypt \
    -v varlibletsencrypt_vol:/var/lib/letsencrypt \
    -v apachesitesavailable_vol:/etc/apache2/sites-available \
    -v videos_vol:/videos \
    -v log_vol:/log \
    -v database_vol:/database \
    smartvision/app:start

(Inside the docker container)
nohup bash /start.bash \
    smartvision.mel@gmail.com \
    cam1.ezicamera.com \
    {ID}.cam1.ezicamera.com \
    k8LE0iCXAseXVk7d \
    &
```

## (Optional and Not Recommended) Manually Build All the Docker Images
1. Build image for WebRTC, in this step we will install WebRTC libraries, python3 pip, python3 websockets.
```
cd ~/ezicamera/Webrtc
sudo docker build . -t smartvision/app:webrtc
```
2. Build image for Web, in this step we will install Apache, deploy pagekite backend and websocket server.
```
cd ~/ezicamera/Web
sudo docker build . -t smartvision/app:web
```
3. Build image for Laravel, in this step will install and config Laravel.
```
cd ~/ezicamera/Laravel
sudo docker build . -t smartvision/app:laravel
```
4. Build the start image, in this step we will compile and deploy the core WebRTC code.
```
cd ~/ezicamera/Start
sudo docker build . -t smartvision/app:start
```
