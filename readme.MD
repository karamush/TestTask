`location / {
     try_files $uri $uri/ /index.php?$args;
 }`
 
 `RewriteEngine on
  RewriteBase /
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . index.php`