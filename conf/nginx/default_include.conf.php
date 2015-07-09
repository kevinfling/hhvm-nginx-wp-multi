  location / {
    # wordpress fancy rewrites
    if (-f $request_filename) {
      break;
    }

    if (-d $request_filename) {
      break;
    }

    rewrite         ^(.+)$ /index.php?q=$1 last;

    # redirect to feedburner.
    # if ($http_user_agent !~ FeedBurner) {
    #   rewrite ^/feed/?$ http://feeds.feedburner.com/feedburner-feed-id last;
    # }
  }

  location ~ .*\.php$ {
    include         /app/vendor/nginx/conf/fastcgi_params;
    fastcgi_pass    unix:/tmp/php-fpm.socket;
    fastcgi_param   SCRIPT_FILENAME $document_root$fastcgi_script_name;
  }

# for people with app root as doc root, restrict access to a few things
location ~ ^/(composer\.|Procfile$|<?=getenv('COMPOSER_VENDOR_DIR')?>/|<?=getenv('COMPOSER_BIN_DIR')?>/) {
    deny all;
}

