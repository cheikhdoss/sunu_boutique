# Sunu Boutique - Frontend

Une application e-commerce moderne développée avec Angular 20 et Angular Material.

## Fonctionnalités

### ✨ Interface utilisateur moderne et responsive
- Design futuriste avec animations fluides
- Thème personnalisé avec Angular Material
- Interface responsive pour tous les appareils

### 🛍️ Catalogue de produits
- Affichage en grille des produits
- Filtrage par catégorie
- Recherche par nom et description
- Images avec fallback automatique

### 📱 Détails des produits
- Page dédiée pour chaque produit
- Informations complètes (prix, stock, description)
- Sélecteur de quantité intelligent
- Badges de statut de stock

### 🛒 Gestion du panier
- Ajout/suppression de produits
- Modification des quantités
- Calcul automatique du total
- Persistance en localStorage
- Badge de notification sur l'icône panier

### 🎨 Design et UX
- Animations CSS personnalisées
- Transitions fluides
- Couleurs et typographie cohérentes
- Icônes Material Design
- États de chargement et messages d'erreur

## Technologies utilisées

- **Angular 20** - Framework principal
- **Angular Material** - Composants UI
- **TypeScript** - Langage de programmation
- **SCSS** - Préprocesseur CSS
- **RxJS** - Programmation réactive

## Structure du projet

```
src/
├── app/
│   ├── components/          # Composants réutilisables
│   │   └── header/         # En-tête avec navigation et panier
│   ├── pages/              # Pages de l'application
│   │   ├── home/           # Page d'accueil avec liste des produits
│   │   ├── product-detail/ # Page de détail d'un produit
│   │   └── cart/           # Page du panier
│   ├── services/           # Services Angular
│   │   ├── product.service.ts  # Gestion des produits et catégories
│   │   └── cart.service.ts     # Gestion du panier
│   └── material.module.ts  # Configuration Angular Material
├── assets/                 # Ressources statiques
├── environments/           # Configuration d'environnement
└── styles/                # Styles globaux et thème
```

## Installation et démarrage

### Prérequis
- Node.js (version 18 ou supérieure)
- npm ou yarn

### Installation
```bash
cd frontend
npm install
```

### Démarrage en mode développement
```bash
npm start
```

L'application sera accessible sur `http://localhost:4200`

### Build de production
```bash
npm run build
```

## Configuration

### API Backend
Modifiez le fichier `src/environments/environment.ts` pour configurer l'URL de l'API :

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'
};
```

### Thème personnalisé
Le thème est configuré dans `src/custom-theme.scss` et utilise les variables CSS définies pour une cohérence visuelle.

## Fonctionnalités détaillées

### Page d'accueil
- Section hero avec call-to-action
- Barre de recherche et filtres par catégorie
- Grille de produits avec images, prix et boutons d'action
- États de chargement et messages d'erreur

### Page produit
- Images haute résolution avec fallback
- Informations détaillées (nom, prix, description, stock)
- Sélecteur de quantité avec validation
- Boutons d'ajout au panier et d'achat rapide
- Caractéristiques du service (livraison, garantie, etc.)

### Panier
- Liste détaillée des articles
- Contrôles de quantité avec validation de stock
- Calcul automatique des totaux
- Résumé de commande avec informations de livraison
- Modes de paiement acceptés

### Navigation
- Header fixe avec logo et navigation
- Badge de panier avec compteur d'articles
- Breadcrumbs et boutons de retour
- Navigation responsive

## Responsive Design

L'application est entièrement responsive avec des breakpoints optimisés :
- **Desktop** : > 1024px
- **Tablet** : 768px - 1024px
- **Mobile** : < 768px

## Performance

- Lazy loading des composants
- Optimisation des images
- Mise en cache des données
- Animations CSS optimisées
- Bundle splitting automatique

## Accessibilité

- Attributs ARIA appropriés
- Navigation au clavier
- Contrastes de couleurs respectés
- Textes alternatifs pour les images

## Développement

### Commandes utiles
```bash
# Démarrage en mode développement
npm start

# Build de production
npm run build

# Tests unitaires
npm test

# Linting
ng lint

# Formatage du code
ng format
```

### Conventions de code
- Utilisation de TypeScript strict
- Nommage en camelCase pour les variables et fonctions
- Nommage en PascalCase pour les classes et interfaces
- Utilisation d'interfaces pour le typage des données

## Intégration avec le backend

L'application communique avec l'API Laravel via les endpoints suivants :
- `GET /api/products` - Liste des produits
- `GET /api/products/{id}` - Détail d'un produit
- `GET /api/categories` - Liste des catégories

Les services Angular gèrent automatiquement :
- La sérialisation/désérialisation des données
- La gestion des erreurs HTTP
- Le cache des requêtes
- Les états de chargement

## Déploiement

Pour déployer l'application :

1. Build de production :
```bash
npm run build
```

2. Les fichiers générés dans `dist/` peuvent être servis par n'importe quel serveur web statique (Nginx, Apache, etc.)

3. Configuration du serveur pour le routing Angular (redirection vers index.html)

## Support et maintenance

- Compatible avec les navigateurs modernes (Chrome, Firefox, Safari, Edge)
- Mises à jour régulières des dépendances
- Tests automatisés pour la stabilité
- Documentation du code pour la maintenance