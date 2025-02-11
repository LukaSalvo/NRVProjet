# NRVProjet
# 🎸 NRV Festival - Web Application  

## 📌 Introduction  
Ce projet a été réalisé dans le cadre d'une **SAE (Situation d'Auto-Apprentissage Évaluée)** à l'**IUT Nancy-Charlemagne**. Nous avons développé une application web permettant la gestion et la visualisation des spectacles du **NRV Festival**.

👨‍💻 **Équipe de développement :**
- **Carette**
- **Rosenkranz**
- **Salvo**
- **Terrier**

---

## 🚀 Objectifs du Projet  
L’objectif principal était de concevoir un **site web interactif** offrant aux utilisateurs la possibilité de **naviguer dans le programme du festival**, d’afficher les détails des spectacles et de gérer les événements via une interface dédiée aux administrateurs.

---

## 📜 Fonctionnalités Implémentées  

### 🎭 **Navigation sur NRV.net**  
✔ **Afficher le programme complet** : liste de tous les spectacles proposés.  
✔ **Filtrer par date** : voir les spectacles d’une journée spécifique.  
✔ **Filtrer par lieu** : afficher les spectacles selon leur emplacement.  
✔ **Filtrer par style de musique** : catégorisation par genre musical.  

### 🎟 **Affichage d’un Spectacle**  
✔ **Consulter les détails** : titre, artiste, durée, style, extrait vidéo/audio.  
✔ **Afficher les spectacles associés** :  
   - Spectacles prévus dans la même soirée.  
   - Spectacles du même lieu.  
   - Spectacles du même style musical.  

### 🛠 **Gestion du Programme (Administrateurs)**  
✔ **Authentification en tant que staff** : connexion avec identifiants spécifiques.  
✔ **Créer et modifier des spectacles et soirées.**  
✔ **Annuler et réactiver une soirée.**  
✔ **Associer des artistes et styles musicaux à un spectacle.**  
✔ **Ajouter un nouvel artiste à la base de données.**  

### 💜 **Expérience Utilisateur**  
✔ **Ajouter un spectacle à ses favoris.**  
✔ **Consulter et gérer sa liste de favoris.**  

---

## 🏗 **Structure du Projet**  

📂 `conf/` → Fichiers de configuration (connexion base de données, paramètres).  
📂 `src/` → Code source de l’application :  
   - 📁 `classes/action/` → Gestion des actions utilisateur (`DisplaySpectacleAction`).  
   - 📁 `classes/auth/` → Gestion de l’authentification (`Authz`, `AuthnProvider`).  
   - 📁 `classes/dispatch/` → Routeur principal (`Dispatcher.php`).  
   - 📁 `classes/repository/` → Gestion des accès BDD (`NRVRepository.php`).  

📂 `public/` → Ressources accessibles (CSS, images, scripts).  

---

## 📜 Licence
Ce projet a été réalisé dans un cadre académique et n'est pas destiné à un usage commercial.



