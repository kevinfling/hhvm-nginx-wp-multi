location ~ /\. {
    deny all;
}

# WORDPRESS : Rewrite rules, sends everything through index.php and keeps the appended query string intact
location / {
    index  index.php index.html index.htm;
    try_files $uri $uri/ /index.php?q=$uri&$args;
}

# SECURITY : Deny all attempts to access PHP Files in the uploads directory
location ~* /(?:uploads|files)/.*\.php$ {
    deny all;
}


# Rewrite rules for WordPress Multi-site.
if (!-e $request_filename) {
    rewrite /wp-admin$ $scheme://$host$uri/ permanent;
    rewrite ^/[_0-9a-zA-Z-]+(/wp-.*) $1 last;
    rewrite ^/[_0-9a-zA-Z-]+(/.*\.php)$ $1 last;
}

# PERFORMANCE : Set expires headers for static files and turn off logging.
    location ~* ^.+\.(js|css|swf|xml|txt|ogg|ogv|svg|svgz|eot|otf|woff|mp4|ttf|rss|atom|jpg|jpeg|gif|png|ico|zip|tgz|gz|rar|bz2|doc|xls|exe|ppt|tar|mid|midi|wav|bmp|rtf)$ {
    access_log off; log_not_found off; expires 30d;
}

# ESSENTIAL : no favicon logs
location = /favicon.ico {
    log_not_found off;
    access_log off;
}

location = /robots.txt {
    allow all;
    index  index.php index.html index.htm;
    try_files $uri $uri/ /index.php?q=$uri&$args;
}

gzip on;
gzip_vary on;