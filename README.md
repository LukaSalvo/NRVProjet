# NRVProjet
NRVProjet
Membres du projet : 
--Terrier Pacome 
--Salvo Luka 
--Rosenkranz Joey 
--Carette Robin
🎸 NRV Festival - Web Application
📌 Introduction

Ce projet a été réalisé dans le cadre d'une SAE (Situation d'Auto-Apprentissage Évaluée) à l'IUT Nancy-Charlemagne. Nous avons développé une application web pour la gestion et la visualisation des spectacles du NRV Festival.

Le projet a été conçu et développé par un groupe de 4 étudiants :

    Carette
    Rosenkranz
    Salvo
    Terrier

🚀 Objectifs du Projet

L’objectif était de concevoir un site web permettant aux utilisateurs de naviguer dans le programme du festival, d'afficher les détails des spectacles et de gérer les événements via une interface administrateur.
📜 Fonctionnalités Implémentées
🎭 Navigation sur NRV.net

✔ Afficher le programme complet : liste complète des spectacles proposés.
✔ Filtrer par date : affichage des spectacles d’une journée spécifique.
✔ Filtrer par lieu : affichage des spectacles par emplacement.
✔ Filtrer par style de musique : classification des spectacles par genre musical.
🎟 Affichage d’un Spectacle

✔ Voir les détails d’un spectacle : titre, artiste, durée, style, extrait vidéo/audio.
✔ Afficher les spectacles associés :

    Spectacles prévus dans la même soirée.
    Spectacles du même lieu.
    Spectacles du même style musical.

🛠 Gestion du Programme (Administrateurs)

✔ S’authentifier en tant que staff : connexion avec identifiants spécifiques.
✔ Créer et modifier des spectacles et soirées.
✔ Annuler et réactiver une soirée.
✔ Associer des artistes et styles musicaux à un spectacle.
✔ Ajouter un nouvel artiste à la base de données lors de la création d’un spectacle.
💜 Expérience Utilisateur

✔ Ajouter un spectacle à ses favoris.
✔ Consulter et gérer sa liste de favoris.
🏗 Structure du Projet

📁 conf/ → Fichiers de configuration (ex: connexion à la base de données).
📁 src/ → Contient l’ensemble du code source :

    classes/action/ → Gestion des actions utilisateur (ex: DisplaySpectacleAction).
    classes/auth/ → Gestion de l’authentification et des rôles (Authz, AuthnProvider).
    classes/dispatch/ → Routeur principal (Dispatcher.php).
    classes/repository/ → Gestion de la base de données (NRVRepository.php).

📁 public/ → Ressources accessibles par le navigateur (CSS, images...).
⚙ Configuration et Installation
📥 Cloner le projet

git clone https://github.com/votre-utilisateur/NRV-Festival.git
cd NRV-Festival

🛠 Installation des dépendances

Le projet utilise Composer pour la gestion des dépendances. Installez-les avec :

composer install

🗄 Configuration de la Base de Données

    Importer la base de données
        Le fichier SQL NRVimport.sql est disponible à la racine du projet.
        Importez-le dans phpMyAdmin ou via MySQL :

    mysql -u root -p db_nrv < NRVimport.sql

    Configurer la connexion à la base de données
        Modifier conf/db_config.php avec vos informations MySQL.

🚀 Lancer le serveur

Pour tester en local avec PHP :

php -S localhost:8000 -t public

Puis ouvrez votre navigateur à l’adresse http://localhost:8000.
🔑 Identifiants d’Administration

    Email : admin@nrvfest.com
    Mot de passe : adminpassword

📜 Licence

Ce projet a été réalisé dans un cadre académique et n'est pas destiné à un usage commercial.

🎸 NRV Festival - Projet universitaire réalisé à l'IUT Nancy-Charlemagne 🎶
