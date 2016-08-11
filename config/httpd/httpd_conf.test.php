server {
	listen       80;
	server_name demo.yrdwin.com;

	root /home/q/system/demo/src/www/;
	index index.html index.htm index.php;
	if (!-e $request_filename) {
		rewrite ^/(.*)$ /index.php last;
	}
	location ~ .*\.(php|php5)?$
	{
		#fastcgi_pass  unix:/dev/shm/php.socket;
		fastcgi_pass  127.0.0.1:9000;
		fastcgi_index index.php;
		include fastcgi.conf  ;
	}

	location ~ /.svn/ {
		deny all;
	}

    access_log /data/logs/access.log combinedio;
    error_log /data/logs/error.log notice;
}

