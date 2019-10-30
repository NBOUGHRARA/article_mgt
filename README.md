# Gestion des article avec commentaires et likes, incluant un API pour la gestion des contact CRUD

# Installation

Cloner le dépot
* run: git clone https://github.com/NBOUGHRARA/article_mgt.git

* cd article_mgt

Installer les dépendances 
* composer install

modifier le fichier .env avec le user et le password de votre base de donnée

Créer les bases de données 
* php bin/console doctrine:database:create

Exécuter les mogrations
* php bin/console doctrine:migrations:migrate

Exécuter les fictures (fake data)
* php bin/console doctrine:fixtures:load --no-interaction

Lancer le serveur 
php -S 127.0.0.1:8000 -t public


#Enjoy
