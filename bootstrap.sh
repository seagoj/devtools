#!/bin/sh

yum install -y libtool-ltdl-devel libxml2-devel curl-devel libpng-devel libjpeg-devel freetype-devel compat-libstdc++-33 vim

if [ ! -d /usr/apache/2.2.25 ]; then
	echo Downloading Apache 2.2.25...
	wget -q -O httpd-2.2.25.tar.gz http://archive.apache.org/dist/httpd/httpd-2.2.25.tar.gz
	tar -xzf httpd-2.2.25.tar.gz && cd httpd-2.2.25
	make clean
	./configure --prefix=/usr/apache/2.2.25 --enable-so --enable-ssl --enable-deflate --enable-rewrite=shared --enable-info --disable-userdir --disable-cgid --disable-cgi --disable-include --enable-mods-shared=most
	make && make install
fi

if [ ! -d FirebirdCS-2.0.7.13318-0.amd64 ]; then
	echo Downloading Firebird 2.0.7...
	wget -q -O FirebirdCS-2.0.7.13318-0.amd64.tar.gz http://downloads.sourceforge.net/project/firebird/firebird-linux-amd64/2.0.7-Release/FirebirdCS-2.0.7.13318-0.amd64.tar.gz?r=http%3A%2F%2Fwww.firebirdsql.org%2Fen%2Ffirebird-2-0%2F&ts=1417594555&use_mirror=iweb
	tar -xzf FirebirdCS-2.0.7.13318-0.amd64.tar.gz && cd FirebirdCS-2.0.7.13318-0.amd64
	./install.sh
fi

if [ ! -d libmcrypt-2.5.7-1.2.el6.rf.x86_64.rpm ]; then
	echo Downloading libmcrypt...
	wget -q -O libmcrypt-2.5.7-1.2.el6.rf.x86_64.rpm http://pkgs.repoforge.org/libmcrypt/libmcrypt-2.5.7-1.2.el6.rf.x86_64.rpm
	rpm -Uvh libmcrypt-2.5.7-1.2.el6.rf.x86_64.rpm
fi


if [ ! -d libmcrypt-devel-2.5.7-1.2.e16.rf.x86_64.rpm ]; then
	echo Downloading libmcrypt-devel...
	wget -q -O libmcrypt-devel-2.5.7-1.2.el6.rf.x86_64.rpm http://pkgs.repoforge.org/libmcrypt/libmcrypt-devel-2.5.7-1.2.el6.rf.x86_64.rpm
	rpm -Uvh libmcrypt-devel-2.5.7-1.2.el6.rf.x86_64.rpm
fi

if [ ! -f /usr/apache/2.2.25/conf/php.ini ]; then
	echo Downloading PHP 5.6.0...
	wget -q -O php-5.6.0.tar.gz http://php.net/get/php-5.6.0.tar.gz/from/this/mirror
	tar -xzf php-5.6.0.tar.gz && cd php-5.6.0
	make clean
	./configure --with-interbase=/opt/firebird --with-interbase=/opt/firebird --with-pdo-mysql --with-pdo-firebird=/opt/firebird --enable-pcntl --enable-sigchild --with-openssl --disable-debug --with-apxs2=/usr/apache/2.2.25/bin/apxs --with-config-file-path=/usr/apache/2.2.25/conf --with-mysql --with-gd --enable-gd-native-ttf --with-jpeg-dir=/usr/lib64 --with-png-dir=/usr/lib64 --with-zlib-dir=/usr/lib64 --with-freetype-dir=/usr/lib64 --enable-sockets --libdir=/usr/lib64 --with-libdir=lib64 --with-mcrypt --with-curl && make
	chmod a+rwx sapi/cli/php && vim -c '1s/\/usr\/bin\/php/sapi\/cli\/php/' -c 'wq' ext/phar/build_precommand.php && make && make install && /usr/apache/2.2.25/bin/apachectl restart && /usr/apache/2.2.25/bin/apachectl restart
fi

if [ ! -f /usr/local/bin/composer ]; then
	echo Downloading Composer...
	curl -sS https://getcomposer.org/installer | php
	mv composer.phar /usr/local/bin/composer
fi

echo "Restart apache: `/usr/apache/2.2.25/bin/apachectl restart`"
