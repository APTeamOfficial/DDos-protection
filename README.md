# DDos Protection
simple **ddos protection** library to make your website paths secure

## How it works ?
this script will verify users by **Google Recaptcha v3** and will authentication them if everything goes right

## How to use ?

you can use this script in two diffrent ways

- all redir
- script redir

## All redir
with this mod, you will redirect everything to **ADP.php** script and after a successful authentication, user will get what he/she looks for
you can do this using htaccess example

## Script redir
with this mod, you will import **ADP.php** in your php scripts and will check for authentication, if everything goes well, you can show reall content

## How to start using this ?
- register your domain to recaptcha v3 
- copy/paste your **SITEKY** and **PRIVATE KEY** in **config.php**
- do one of supported mods (all redir, script redir)
- create new database and import db_example.sql into it
- change DB_NAME, DB_PASSWORD, DB_HOST and DB_DATABASE in **config.php** acording to your database information
- replace SCRIPT_PATH with path where script exists (ADP.php)
- change EXPIRE_TIME with your custom authentication timeout (in secconds)(after this time, client cookie will be burned and reauthentication needed)
- thats it, your website is secure now :)


### support / suggestion = ph09nixom@gmail.com - t.me/ph09nix
### Leave a STAR if you found this usefull :)
