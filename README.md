# Projet : Le Blog de Batman

## Installation 

### Cloner le projet
```
git remote add origin https://github.com/AurelieBnc/leblogdebatman.git
```

### Modifier les paramètres d'environnement dans le fichier .env 

- Décommenter la ligne DATABASE mysql
- Mettre en commantaire la ligne DATABASE postgresql
- Changer user_db et password_db, nom de la database
```dotenv
# DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=13&charset=utf8"
```
# Clés Google Recaptcha à changer
GOOGLE_RECAPTCHA_SITE_KEY=XXXXXXXXXXXXXXXXXXXXXXXXXXXX
GOOGLE_RECAPTCHA_PRIVATE_KEY=XXXXXXXXXXXXXXXXXXXXXXXXXXXX

### Déplacer le terminal dans le projet cloné
```
cd leblogdebatman
```

### Taper les commandes suivantes

```
composer install
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
php bin/console ckeditor:install
php bin/console assets:install public
php bin/console doctrine:fixtures:load
```

### Démarrer le serveur Web
symfony serve

Les fixtures créeront :
* Un compte admin  (email: admin@a.a , password: aaaaaaaaA7/)
* 50 comptes utilisateurs  (email alétoire, password : aaaaaaaaA7/)
* 200 articles
* entre 0 et 10 commentaires par article

