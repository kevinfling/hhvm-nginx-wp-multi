# for people with app root as doc root, restrict access to a few things
location ~ ^/(composer\.|Procfile$|<?=getenv('COMPOSER_VENDOR_DIR')?>/|<?=getenv('COMPOSER_BIN_DIR')?>/) {
    deny all;
}

location / {
  gzip on;
  gzip_disable "msie6";

  gzip_vary on;
  gzip_proxied any;
  gzip_comp_level 6;
  gzip_buffers 16 8k;
  gzip_http_version 1.1;
  gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript;

  index  index.php;
  rewrite ^wp-json/(.+)$ /index.php?json_route=$1 last;

  # wordpress fancy rewrites
  if (-f $request_filename) {
    break;
  }

  if (-d $request_filename) {
    break;
  }

  rewrite ^(.+)$ /index.php?q=$1 last;

}
