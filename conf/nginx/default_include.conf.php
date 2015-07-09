# for people with app root as doc root, restrict access to a few things
location ~ ^/(composer\.|Procfile$|<?=getenv('COMPOSER_VENDOR_DIR')?>/|<?=getenv('COMPOSER_BIN_DIR')?>/) {
    deny all;
}

location / {

  gzip  on;
  gzip_http_version 1.1;
  gzip_vary on;
  gzip_comp_level 6;
  gzip_proxied any;
  gzip_types text/plain text/html text/css application/json application/javascript application/x-javascript text/javascript text/xml application/xml application/rss+xml application/atom+xml application/rdf+xml;

  # make sure gzip does not lose large gzipped js or css files
  gzip_buffers 16 8k;

  # Disable gzip for "certain" (ahem) browsers.
  gzip_disable msie6;
  
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
