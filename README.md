# BookShelf — Bibliothèque en ligne (Symfony 7.4)

Application web de gestion de livres avec interface **AdminLTE 4** (assets dans `public/adminlte/`), API REST (**API Platform**), authentification, liste de lecture en session, envoi d’e-mails et tests PHPUnit.

## Prérequis

- PHP 8.2+
- Composer
- Extension SQLite (par défaut) ou adapter `DATABASE_URL` pour MySQL/PostgreSQL

## Installation

```bash
composer install
cp .env .env.local   # optionnel
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

Créez le répertoire d’upload si besoin : `mkdir -p public/uploads/livres` (déjà versionné avec `.gitkeep`).

Lancez le serveur :

```bash
symfony server:start
# ou
php -S localhost:8000 -t public
```

Ouvrez `http://localhost:8000` (ou l’URL du serveur Symfony). La documentation API / Swagger est sur **`/api`**.

## Mailtrap (e-mails)

Dans `.env.local`, configurez le DSN Mailtrap (onglet **Email Testing** → **Integration** → **Symfony 5+**) :

```env
MAILER_DSN=smtp://USERNAME:PASSWORD@sandbox.smtp.mailtrap.io:2525?encryption=tls&auth_mode=login
BOOKSHELF_NOTIFY_EMAIL=votre-adresse-de-reception@example.com
```

Les notifications « nouveau livre » partent de `noreply@bookshelf.com` vers `BOOKSHELF_NOTIFY_EMAIL`.

## Comptes de test (fixtures)

| Rôle               | Email               | Mot de passe |
|--------------------|---------------------|--------------|
| Administrateur     | admin@bookshelf.com | admin123     |
| Bibliothécaire     | biblio@bookshelf.com | biblio123   |
| Utilisateur (×5) | (emails Faker)      | user12345    |

Données : 6 genres, 8 tags, 5 auteurs, 30 livres, 7 utilisateurs.

### Inscription et rôles

Sur `/register`, le formulaire propose un **type de compte** : *Lecteur* (`ROLE_USER`) ou *Bibliothécaire* (`ROLE_BIBLIOTHECAIRE`). Le rôle **admin** n’est pas choisissable à l’inscription (conformément au contrôle d’accès du sujet) : utilisez le compte `admin@bookshelf.com` des fixtures ou modifiez les rôles en base pour les besoins pédagogiques.

## Tests

Prépare la base de test et exécute PHPUnit :

```bash
composer test
```

Équivalent manuel :

```bash
php bin/console doctrine:migrations:migrate --no-interaction --env=test
php bin/console doctrine:fixtures:load --no-interaction --env=test
php bin/phpunit
```

## Schéma des relations (entités)

```mermaid
erDiagram
    USER ||--o{ LIVRE : ajoute
    AUTEUR ||--o{ LIVRE : ecrit
    GENRE ||--o{ LIVRE : classe
    LIVRE }o--o{ TAG : etiquete
```

## AdminLTE

Le thème **AdminLTE 4.0.0-rc7** est servi depuis `public/adminlte/` (copie du dossier `dist/`). Le layout principal est `templates/base.html.twig` (navbar, sidebar, contenu, footer).

## Commande utile

```bash
php bin/console app:bookshelf:stats
php bin/console app:bookshelf:stats --detail --format=json
```
