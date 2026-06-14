## Installation

### 1. Cloner le projet

```
git clone git@github.com:Valgrm/WR602D-Back.git
cd wr602d-back
```

### 2. Installer les dépendances

```
composer install
```

### 3. Configurer l'environnement

```
cp .env .env.local
```

Modifier `.env.local` :

```
APP_ENV=dev
APP_SECRET=une_chaine_aleatoire_32_caracteres
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/wr602d?serverVersion=16&charset=utf8"
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

### 4. Générer les clés JWT

```
php bin/console lexik:jwt:generate-keypair
```

### 5. Créer la base de données

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. Lancer le serveur de développement

```
symfony server:start
# ou
php -S localhost:8000 -t public/
```

L'API est accessible sur `http://localhost:8000/api`.
