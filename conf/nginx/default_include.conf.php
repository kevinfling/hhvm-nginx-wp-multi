location / {
  try_files $uri $uri/ /index.php?$args;
}

# for people with app root as doc root, restrict access to a few things
location ~ ^/(composer\.|Procfile$|<?=getenv('COMPOSER_VENDOR_DIR')?>/|<?=getenv('COMPOSER_BIN_DIR')?>/) {
    deny all;
}

# Add trailing slash to */wp-admin requests.
rewrite /wp-admin$ $scheme://$host$uri/ permanent;

# Directives to send expires headers and turn off 404 error logging.
location ~* ^.+\.(ogg|ogv|svg|svgz|eot|otf|woff|mp4|ttf|rss|atom|jpg|jpeg|gif|png|ico|zip|tgz|gz|rar|bz2|doc|xls|exe|ppt|tar|mid|midi|wav|bmp|rtf)$ {
       access_log off; log_not_found off; expires max;
}

# Uncomment one of the lines below for the appropriate caching plugin (if used).
#include global/wordpress-wp-super-cache.conf;
#include global/wordpress-w3-total-cache.conf;

# Pass all .php files onto a php-fpm/php-fcgi server.
location ~ [^/]\.php(/|$) {
  fastcgi_split_path_info ^(.+?\.php)(/.*)$;
  if (!-f $document_root$fastcgi_script_name) {
    return 404;
  }
  # This is a robust solution for path info security issue and works with "cgi.fix_pathinfo = 1" in /etc/php.ini (default)

  include fastcgi.conf;
  fastcgi_index index.php;
# fastcgi_intercept_errors on;
  fastcgi_pass php;
}
