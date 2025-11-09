# Dashboard Professionnel

Un dashboard moderne et responsive avec barre de navigation pliable, développé avec Tailwind CSS.

## Fonctionnalités

✨ **Design moderne et professionnel**
- Interface utilisateur élégante avec Tailwind CSS
- Couleurs et typographie soigneusement choisies
- Animations fluides et transitions smooth

🔧 **Barre de navigation pliable**
- Sidebar qui se masque/affiche avec un simple clic
- Adaptation automatique sur mobile
- Icônes Feather Icons pour un look professionnel

📊 **Composants de dashboard**
- Cartes de statistiques avec icônes colorées
- Tableau responsive des commandes récentes
- Zone pour graphiques (prête pour Chart.js)
- Feed d'activité en temps réel

📱 **Responsive Design**
- Optimisé pour tous les appareils
- Navigation mobile adaptative
- Grille responsive avec Tailwind CSS

## Structure des fichiers

```
maquette/
├── index.html          # Page principale du dashboard
├── script.js           # Logique JavaScript pour l'interactivité
├── styles.css          # Styles CSS personnalisés
└── README.md           # Documentation du projet
```

## Installation et utilisation

1. **Cloner ou télécharger** les fichiers dans votre dossier de projet

2. **Ouvrir** le fichier `index.html` dans votre navigateur web

3. **Personnaliser** selon vos besoins :
   - Modifier les couleurs dans la configuration Tailwind
   - Ajouter vos propres données dans les cartes statistiques
   - Intégrer vos graphiques avec Chart.js ou D3.js
   - Connecter à votre API backend

## Fonctionnalités principales

### Navigation pliable
- Cliquez sur l'icône menu pour réduire/étendre la sidebar
- La sidebar se réduit automatiquement sur mobile
- Animations fluides avec CSS transitions

### Sections disponibles
- 📈 **Tableau de bord** - Vue d'ensemble des métriques
- 👥 **Utilisateurs** - Gestion des utilisateurs
- 📊 **Analytiques** - Rapports et analyses
- 🛒 **Commandes** - Gestion des commandes
- 📦 **Produits** - Catalogue produits
- 💳 **Paiements** - Transactions financières
- ⚙️ **Paramètres** - Configuration système

### Cartes de statistiques
- Revenus totaux avec pourcentage d'évolution
- Nombre de commandes
- Nombre d'utilisateurs
- Taux de conversion

## Personnalisation

### Couleurs
Modifiez les couleurs dans la configuration Tailwind :
```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#3B82F6',    // Bleu principal
                secondary: '#F1F5F9',  // Gris clair
                accent: '#10B981'      // Vert accent
            }
        }
    }
}
```

### Ajout de nouvelles sections
1. Ajoutez un nouvel élément dans la navigation sidebar
2. Mettez à jour la fonction `updateMainContent()` dans `script.js`
3. Créez le contenu correspondant

### Intégration de graphiques
Le dashboard est prêt pour intégrer des bibliothèques comme :
- **Chart.js** pour des graphiques simples
- **D3.js** pour des visualisations complexes
- **ApexCharts** pour des graphiques interactifs

## Technologies utilisées

- **HTML5** - Structure sémantique
- **Tailwind CSS** - Framework CSS utilitaire
- **JavaScript ES6+** - Logique d'interaction
- **Feather Icons** - Icônes vectorielles
- **CSS3** - Animations et transitions personnalisées

## Responsive Breakpoints

- **Mobile** : < 768px (navigation overlay)
- **Tablet** : 768px - 1024px (navigation adaptée)
- **Desktop** : > 1024px (navigation complète)

## Navigateurs supportés

✅ Chrome (recommandé)
✅ Firefox
✅ Safari
✅ Edge
✅ Opera

## Prochaines étapes

Pour un projet en production, considérez :

1. **Backend Integration**
   - API REST pour les données
   - Authentification utilisateur
   - Base de données

2. **Fonctionnalités avancées**
   - Mode sombre complet
   - Notifications push
   - Recherche avancée
   - Filtres et tri

3. **Optimisations**
   - Lazy loading des composants
   - Service Worker pour le cache
   - Optimisation des images

4. **Tests**
   - Tests unitaires JavaScript
   - Tests d'intégration
   - Tests de responsive design

## Licence

Ce projet est libre d'utilisation pour vos projets personnels et commerciaux.

---

**Développé avec ❤️ et Tailwind CSS**