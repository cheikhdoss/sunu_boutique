# 🚀 Guide de Démarrage - Sunu Boutique Frontend

## ✅ Problèmes résolus

### 1. **Erreur localStorage (SSR)**
- ✅ **Problème** : `localStorage is not defined` lors du rendu côté serveur
- ✅ **Solution** : Ajout de vérifications `isPlatformBrowser()` dans le CartService
- ✅ **Résultat** : Le panier fonctionne maintenant correctement côté client

### 2. **Erreur thème SCSS**
- ✅ **Problème** : Fonctions Material Design non définies
- ✅ **Solution** : Simplification du thème avec variables CSS uniquement
- ✅ **Résultat** : Compilation sans erreur avec design moderne conservé

### 3. **Configuration SSR simplifiée**
- ✅ **Problème** : Complexité du rendu côté serveur pour le développement
- ✅ **Solution** : Configuration en mode `static` pour le développement
- ✅ **Résultat** : Démarrage plus rapide et stable

## 🎯 Comment démarrer l'application

### Option 1 : Commande standard
```bash
cd frontend
npm start
```

### Option 2 : Script personnalisé
```bash
cd frontend
node start.js
```

### Option 3 : Commande directe
```bash
cd frontend
ng serve --configuration development
```

## 🌐 Accès à l'application

Une fois démarrée, l'application sera accessible sur :
- **URL locale** : http://localhost:4200
- **URL réseau** : http://0.0.0.0:4200

## 📱 Fonctionnalités testables

### 1. **Page d'accueil** (`/`)
- ✅ Liste des produits avec images
- ✅ Filtrage par catégorie
- ✅ Recherche par nom/description
- ✅ Ajout au panier avec notification

### 2. **Détails produit** (`/product/:id`)
- ✅ Informations complètes du produit
- ✅ Sélecteur de quantité
- ✅ Ajout au panier avec validation stock
- ✅ Navigation retour

### 3. **Panier** (`/cart`)
- ✅ Liste des articles ajoutés
- ✅ Modification des quantités
- ✅ Suppression d'articles
- ✅ Calcul automatique des totaux
- ✅ Persistance des données

### 4. **Navigation**
- ✅ Header avec logo et menu
- ✅ Badge panier avec compteur
- ✅ Navigation responsive
- ✅ Breadcrumbs

## 🎨 Design et UX

### **Thème moderne**
- Couleurs : Bleu primaire (#3f51b5) et Rose accent (#e91e63)
- Typographie : Inter font pour un look moderne
- Animations : Transitions fluides et hover effects
- Responsive : Optimisé mobile, tablette, desktop

### **Composants Material**
- Cards avec ombres et animations
- Boutons avec états visuels
- Form fields avec validation
- Spinners de chargement
- Snackbars pour les notifications

## 🔧 Données de test

L'application inclut un service de données de test (`MockDataService`) avec :
- **12 produits** variés avec images Unsplash
- **5 catégories** (Électronique, Vêtements, Maison, Sports, Livres)
- **Simulation de délais** réseau réalistes
- **Gestion du stock** avec produits en rupture

## 🐛 Résolution des erreurs courantes

### **Si l'application ne démarre pas**
1. Vérifiez que Node.js est installé (version 18+)
2. Supprimez `node_modules` et relancez `npm install`
3. Utilisez `npm start` au lieu de `ng serve`

### **Si les styles ne s'affichent pas**
1. Vérifiez que les imports CSS sont corrects
2. Rechargez la page avec Ctrl+F5
3. Vérifiez la console pour les erreurs CSS

### **Si le panier ne fonctionne pas**
1. Vérifiez que localStorage est activé dans le navigateur
2. Ouvrez les DevTools et vérifiez la console
3. Le panier fonctionne uniquement côté client (pas en SSR)

## 📊 Performance

### **Optimisations incluses**
- Lazy loading des composants
- Optimisation des images avec fallback
- Bundle splitting automatique
- Mise en cache des requêtes HTTP
- Animations CSS optimisées

### **Métriques attendues**
- **Temps de démarrage** : ~3-5 secondes
- **Temps de chargement** : ~1-2 secondes
- **Taille du bundle** : ~500KB (gzippé)
- **Score Lighthouse** : 90+ (Performance)

## 🔄 Intégration Backend

### **APIs supportées**
```typescript
// Produits
GET /api/products          // Liste des produits
GET /api/products/{id}     // Détail d'un produit
GET /api/categories        // Liste des catégories

// Configuration dans environment.ts
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'
};
```

### **Fallback automatique**
Si le backend n'est pas disponible, l'application utilise automatiquement les données de test sans interruption de service.

## 🎉 Prêt à utiliser !

L'application frontend est maintenant **100% fonctionnelle** avec :
- ✅ Design moderne et responsive
- ✅ Gestion complète du panier
- ✅ Navigation fluide
- ✅ Données de test intégrées
- ✅ Aucune erreur de compilation
- ✅ Performance optimisée

**Commande de démarrage** :
```bash
cd frontend && npm start
```

**URL d'accès** : http://localhost:4200

---

*Développé avec Angular 20, Angular Material et beaucoup de ❤️*