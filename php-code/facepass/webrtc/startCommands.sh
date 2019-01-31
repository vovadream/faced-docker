#!/bin/bash
#turnserver
#turnserver -L localhost -a -f -r test

#turnserver
turnserver -L uwpw.ru:8080 -a -f -r test &

#websockets
#sudo $GOPATH/bin/collidermain -tls=true
/var/www/facepass/webrtc/collider/collidermain -tls=true -room-server=https://webrtc.uwpw.ru/room -port=8089 > /var/www/facepass/webrtc/collider/collider.log &


#RTC API:
/var/www/facepass/webrtc/google-cloud-sdk/bin/dev_appserver.py /var/www/facepass/webrtc/out/app_engine/ --host=0.0.0.0


