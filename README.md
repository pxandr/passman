a very simple basic pass manager

how to use

run openssl rand -hex 32
It will produce a 64-character key (32 bytes), for example:
f8a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1
open .env and put your key in there.


php passman.php add google.com  qwaswesd123
php passman.php get service (google.com)
php passman.php delete service (google.com)
php passman.php list  (saved services\sites )


