Guide d’installation et d’utilisation — Discord Clone (Symfony)
Ce projet est un clone de Discord développé avec Symphony 8. Il permet de s’inscrire, De se connecter, de créer et de supprimer des salons textuels, ainsi que d’échanger des messages en temps réel au sein de ces salons.

🛠️ 1. Prérequis
Avant de commencer, Assure-toi d’avoir installé sur ta machine :

PHP (version 8.2 ou supérieure)

Compositeur (le gestionnaire de dépendances PHP)

Symfony CLI (recommandé) ou un serveur web local

MySQL ou MariaDB (pour la base de données)

⚙️ 2. Installation étape par étape
Ouvre ton terminal et suis ces instructions :

Cloner le dépôt GitHub :

Bash
git clone https://github.com/G-Cyril-P/Discord-Symfony.git
cd discord-clone
Installer les dépendances PHP :

Bash
composer install
Configurer la base de données :

Duplique le fichier  ou crée un fichier  à la racine..env.env.local

Modifie la ligne  pour y inscrire tes identifiants MySQL :DATABASE_URL

Extrait de code
DATABASE_URL="mysql://root:@127.0.0.1:3306/discord_clone?serverVersion=8.0.32&charset=utf8mb4"
Créer la base de données et lancer les migrations :

Bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
🚀 3. Lancer l’application
Démarre le serveur local Symfony avec la commande suivante :

Bash
symfony server:start
(Ou utilise si tu n’as pas la CLI Symfony).php -S localhost:8000 -t public/

Ouvre ensuite ton navigateur à l’adresse indiquée (généralement ).[http://127.0.0.1:8000](http://127.0.0.1:8000)

💡 4. Utilisation
Inscription / Connexion :

Lors du premier accès, crée un compte via la page d’inscription.

Connecte-toi avec tes identifiants (sécurisés par ).ROLE_USER

Gestion des salons :

Navigue entre les différents salons disponibles.

Crée un nouveau salon à l’aide du bouton dédié.

Modifie ou supprime un salon existant (les messages associés sont automatiquement nettoyés grâce à la gestion des entités en cascade).

Messagerie :

Tape ton message dans le champ prévu en bas de la page du salon et clique sur Envoyer pour l’ajouter instantanément au fil de discussion.
