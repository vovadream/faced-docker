FROM ubuntu
MAINTAINER vovadream <admin@vovadream.ru>


RUN DEBIAN_FRONTEND=noninteractive apt-get update && apt upgrade -y && apt install -y apt-utils dialog \
				build-essential g++ python-dev autotools-dev libicu-dev build-essential \
				libbz2-dev wget libjpeg62 unzip \
				&& wget https://sourceforge.net/projects/boost/files/boost/1.67.0/boost_1_67_0.tar.gz \
				&& tar xzvf boost_1_67_0.tar.gz \
				&& cd boost_1_67_0/ && ./bootstrap.sh --prefix=/usr/local \
				&& ./b2 --with=all -j 4 install && sh -c 'echo "/usr/local/lib" >> /etc/ld.so.conf.d/local.conf' \
				&& ldconfig && cd .. && rm -rf boost_1_67_0/ \
				&& wget https://raw.githubusercontent.com/vovadream/scripts/master/installopencv.sh \
				&& DEBIAN_FRONTEND=noninteractive sh installopencv.sh \
				&& DEBIAN_FRONTEND=noninteractive apt install -y software-properties-common \
				&& apt-add-repository "deb http://dk.archive.ubuntu.com/ubuntu/ xenial main" \
				&& apt-add-repository "deb http://dk.archive.ubuntu.com/ubuntu/ xenial universe" \
				&& apt-get update && apt-get install -y gcc-4.9 && apt-get upgrade libstdc++6 \
				&& wget https://sourceforge.net/projects/libpng/files/libpng15/1.5.30/libpng-1.5.30.tar.gz \
				&& tar xzvf libpng-1.5.30.tar.gz && rm libpng-1.5.30.tar.gz  && cd libpng-1.5.30/ \
				&& ./configure --prefix=/usr/local/libpng && make check && make install && make check \
				&& ln -s /usr/local/libpng/lib/libpng15.so.15 /usr/lib/libpng15.so.15 \
				&& cd / && rm boost_1_67_0.tar.gz && rm installopencv.sh && rm -r OpenCV/ && rm -r libpng-1.5.30/ \
				&& apt purge -y apt-utils dialog build-essential g++ python-dev autotools-dev libicu-dev build-essential \
				libbz2-dev wget unzip \
                                build-essential cmake qt5-default libvtk6-dev zlib1g-dev libjpeg-dev libwebp-dev libpng-dev libtiff5-dev \
			   	libjasper-dev libopenexr-dev libgdal-dev libdc1394-22-dev libavcodec-dev libavformat-dev libswscale-dev \
			   	libtheora-dev libvorbis-dev libxvidcore-dev libx264-dev yasm libopencore-amrnb-dev libopencore-amrwb-dev \
			   	libv4l-dev libxine2-dev libtbb-dev libeigen3-dev python-dev python-tk python-numpy python3-dev python3-tk \
			   	python3-numpy ant default-jdk doxygen software-properties-common \
				&& apt autoremove -y \
				&& apt install -y libpq5 libqt5test5 libqt5opengl5 libtbb2 libdc1394-22 libxine2 libswscale4 \
				&& apt autoremove -y
#COPY faced.service /etc/systemd/system/
#COPY face_method6.6/ /home/face
WORKDIR /home/face
CMD ["/home/face/FaceDetector"]
EXPOSE 8085
