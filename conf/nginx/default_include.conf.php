# for people with app root as doc root, restrict access to a few things
location ~ ^/(composer\.|Procfile$|<?=getenv('COMPOSER_VENDOR_DIR')?>/|<?=getenv('COMPOSER_BIN_DIR')?>/) {
    deny all;
}

location / {

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
