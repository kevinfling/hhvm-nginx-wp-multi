location / {
  try_files $uri $uri/ /index.php?$args;
}

if (!-e $request_filename) { rewrite ^.* /index.php break; }

# for people with app root as doc root, restrict access to a few things
location ~ ^/(composer\.|Procfile$|<?=getenv('COMPOSER_VENDOR_DIR')?>/|<?=getenv('COMPOSER_BIN_DIR')?>/) {
    deny all;
}

# Add trailing slash to */wp-admin requests.
rewrite /wp-admin$ $scheme://$host$uri/ permanent;

location ~ ^/files/(.*)$ {
  try_files /wp-content/blogs.dir/$blogid/$uri /wp-includes/ms-files.php?file=$1 ;
  access_log off; log_not_found off; expires max;
}

location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
  expires 24h;
  log_not_found off;
}

location ^~ /blogs.dir {
  internal;
  alias /var/www/pathtoyoursite/web/wp-content/blogs.dir ;
  access_log off; log_not_found off;      expires max;
}

#if (!-e $request_filename) {
#  rewrite /wp-admin$ $scheme://$host$uri/ permanent;
#  rewrite ^/[_0-9a-zA-Z-]+(/wp-.*) $1 last;
#  rewrite ^/[_0-9a-zA-Z-]+(/.*\.php)$ $1 last;
#}

# Directives to send expires headers and turn off 404 error logging.
#location ~* ^.+\.(ogg|ogv|svg|svgz|eot|otf|woff|mp4|ttf|rss|atom|jpg|jpeg|gif|png|ico|zip|tgz|gz|rar|bz2|doc|xls|exe|ppt|tar|mid|midi|wav|bmp|rtf)$ {
#  access_log off; log_not_found off; expires max;
#}

# Pass all .php files onto a php-fpm/php-fcgi server.
location ~ \.php$ {
  # Zero-day exploit defense.
  # http://forum.nginx.org/read.php?2,88845,page=3
  # Won't work properly (404 error) if the file is not stored on this server, which is entirely possible with php-fpm/php-fcgi.
  # Comment the 'try_files' line out if you set up php-fpm/php-fcgi on another machine.  And then cross your fingers that you won't get hacked.
  try_files $uri =404;

  fastcgi_split_path_info ^(.+\.php)(/.+)$;
  #NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini

  include fastcgi_params;
  fastcgi_index index.php;
  fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
# fastcgi_intercept_errors on;
  fastcgi_pass php;
}
