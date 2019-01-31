#!/bin/sh
/var/www/facepass/webrtc/google-cloud-sdk/bin/dev_appserver.py /var/www/facepass/webrtc/out/app_engine/ --host=0.0.0.0  >> /var/www/facepass/webrtc/services/webrtc/webrtc.log
