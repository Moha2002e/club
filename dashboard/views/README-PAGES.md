# Dashboard HEPL Tech Lab - Structure des Pages

## 📁 Structure des Fichiers HTML

Le dashboard a été séparé en **7 pages HTML distinctes** :

### Pages Principales

1. **dashboard.html** - Page d'accueil
   - Statistiques globales
   - Cartes de métriques (projets, événements, membres, messages)
   - Aperçu des prochains événements
   - Dernières discussions

2. **projects.html** - Gestion des Projets
   - Tableau des projets en cours
   - Progression et statuts
   - Gestion des membres par projet
   - Actions : Terminer, Modifier, Supprimer

3. **members.html** - Gestion des Membres
   - Liste complète des membres
   - Rôles et permissions
   - Informations de contact
   - Participation aux projets

4. **events.html** - Calendrier des Événements
   - Vue calendrier (à intégrer avec FullCalendar.js)
   - Événements à venir
   - Gestion des réunions

5. **tasks.html** - Gestion des Tâches
   - Liste des tâches
   - Assignment et suivi
   - (En développement)

6. **messages.html** - Messagerie Interne
   - Chat entre membres
   - Messages et notifications
   - (En développement)

7. **settings.html** - Paramètres
   - Configuration du club
   - Authentification et permissions
   - Profils utilisateurs

## 🎨 Fichiers CSS et JavaScript

- **styles.css** - Styles personnalisés
- **script.js** - Logique principale (sidebar, notifications, actions)
- **tabs.js** - (Ancien fichier, peut être supprimé)

## 🔄 Navigation

Chaque page contient :
- ✅ Sidebar complète avec navigation
- ✅ L'élément actif est mis en surbrillance automatiquement
- ✅ Header avec recherche et notifications
- ✅ Responsive (mobile et desktop)

## 📱 Fonctionnalités

### Sidebar
- **Desktop** : Sidebar rétractable/extensible
- **Mobile** : Sidebar en overlay avec fermeture par clic extérieur ou touche Escape

### Navigation
- Liens directs entre les pages
- Indicateur visuel de la page active
- Fermeture automatique de la sidebar mobile lors de la navigation

### Actions Projets (page projects.html)
- ✅ Marquer un projet comme terminé
- ✅ Modifier un projet
- ✅ Supprimer un projet
- ✅ Notifications de confirmation

## 🚀 Utilisation

1. Ouvrir **dashboard.html** comme page d'accueil
2. Naviguer entre les pages via la sidebar
3. Chaque page fonctionne de manière indépendante

## 🎯 Prochaines Étapes

- [ ] Intégrer FullCalendar.js pour la page événements
- [ ] Développer la section tâches
- [ ] Développer la messagerie interne
- [ ] Ajouter un système d'authentification
- [ ] Connecter à une base de données backend

## 📝 Notes Techniques

- Framework CSS : **Tailwind CSS** (via CDN)
- Icônes : **Feather Icons**
- Responsive : **Mobile-first design**
- JavaScript vanilla (pas de framework)

