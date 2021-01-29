# EziCamera
Do you want to make your Raspberry Pi a security camera?  This is the right place.

## Preparation
Following list is what you need to prepare before running the app:
1. Raspberry Pi OS is the operating system.
2. Camera is installed and enabled on your Raspberry Pi.
2. Your Raspberry Pi has Internet access.
3. Docker is installed on your Raspberry Pi.

## Run
Enter the following command to run the app:
```bash
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
    {ID}.cam1.ezicamera.com \
    k8LE0iCXAseXVk7d \
    {Your Email} \
    {Your Password}
```

## Manually build the docker image
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
