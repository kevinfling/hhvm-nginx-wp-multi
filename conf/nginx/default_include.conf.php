
# for people with app root as doc root, restrict access to a few things
location ~ ^/(composer\.|Procfile$|<?=getenv('COMPOSER_VENDOR_DIR')?>/|<?=getenv('COMPOSER_BIN_DIR')?>/) {
    deny all;
}

# WordPress multisite subdirectory rules.
# Designed to be included in any server {} block.

# This order might seem weird - this is attempted to match last if rules below fail.
# http://wiki.nginx.org/HttpCoreModule
location / {
  try_files $uri $uri/ /index.php?$args;
}

# Directives to send expires headers and turn off 404 error logging.
location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
  expires 24h;
  log_not_found off;
}

location ~ ^/[_0-9a-zA-Z-]+/files/(.*)$ {
        try_files /wp-content/blogs.dir/$blogid/files/$2 /wp-includes/ms-files.php?file=$2 ;
        access_log off; log_not_found off; expires max;
}

# Uncomment one of the lines below for the appropriate caching plugin (if used).
#include global/wordpress-ms-subdir-wp-super-cache.conf;
#include global/wordpress-ms-subdir-w3-total-cache.conf;

# Rewrite multisite '.../wp-.*' and '.../*.php'.
if (!-e $request_filename) {
  rewrite /wp-admin$ $scheme://$host$uri/ permanent;
  rewrite ^/[_0-9a-zA-Z-]+(/wp-.*) $1 last;
  rewrite ^/[_0-9a-zA-Z-]+(/.*\.php)$ $1 last;
}
