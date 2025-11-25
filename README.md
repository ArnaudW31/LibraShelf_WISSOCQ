# Bienvenue sur LibraShelf !
par Arnaud Wissocq

## Prérequis :
- PHP 8.2 Minimum
- Composer
- Un système de base de données SQL (de préférence Postgresql)

## Installation :
1. Installer Symfony et toutes les dépendances avec la commande
composer install

2. Créez un fichier .env.local et mettez-y les informations de votre base de données SQL et autres

3. Créez la base de données avec la commande php 
bin/console doctrine:database:create

4. Lancez les migrations avec la commande 
php bin/console doctrine:migrations:migrate


5. Chargez les fixtures avec la commande 
php bin/console doctrine:fixtures:load
OU importez vos données (bonne chance)

Une fois toutes ces étapes terminées, vous pouvez démarrer le serveur avec symfony server:start !

### Bonne lecture !
(Bonne correction...)