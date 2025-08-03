# 📐 Grille 3 Colonnes - Section Collections

## 🎯 Configuration de la grille

La section "Découvrez nos Collections" utilise maintenant une **grille de 3 produits par ligne** optimisée pour différentes tailles d'écran.

## 📱 Responsive Design

### 🖥️ **Desktop Large (>1400px)**
```css
grid-template-columns: repeat(3, 1fr);
gap: 50px;
max-width: 1600px;
```
- **3 colonnes** avec espacement généreux
- Images : **320px** de hauteur
- Aspect ratio : **0.85** (plus élancé)

### 🖥️ **Desktop Standard (1200px-1400px)**
```css
grid-template-columns: repeat(3, 1fr);
gap: 40px;
max-width: 1300px;
```
- **3 colonnes** standard
- Images : **280px** de hauteur
- Aspect ratio : **0.8** (équilibré)

### 💻 **Desktop Compact (992px-1199px)**
```css
grid-template-columns: repeat(3, 1fr);
gap: 30px;
max-width: 1100px;
```
- **3 colonnes** compactes
- Images : **240px** de hauteur
- Texte réduit pour optimiser l'espace

### 📱 **Tablette (768px-991px)**
```css
grid-template-columns: repeat(2, 1fr);
gap: 30px;
max-width: 800px;
```
- **2 colonnes** pour tablettes
- Images : **260px** de hauteur
- Aspect ratio : **0.9**

### 📱 **Mobile Large (576px-767px)**
```css
grid-template-columns: repeat(2, 1fr);
gap: 20px;
max-width: 600px;
```
- **2 colonnes** compactes
- Images : **200px** de hauteur
- Aspect ratio : **1** (carré)

### 📱 **Mobile Small (<576px)**
```css
grid-template-columns: 1fr;
gap: 20px;
max-width: 400px;
```
- **1 colonne** pour petits écrans
- Images : **220px** de hauteur
- Aspect ratio : **1.1**

### 🖥️ **Écrans très larges (>1800px)**
```css
grid-template-columns: repeat(4, 1fr);
gap: 60px;
max-width: 1800px;
```
- **4 colonnes** pour écrans ultra-larges
- Espacement maximal

## 🎨 **Optimisations visuelles**

### 📏 **Uniformisation des hauteurs**
```css
.collection-card {
  aspect-ratio: 0.8;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
```

### 📝 **Texte optimisé**
```css
/* Titre - Maximum 2 lignes */
.collection-name {
  -webkit-line-clamp: 2;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Description - Maximum 3 lignes */
.collection-description {
  -webkit-line-clamp: 3;
  overflow: hidden;
  text-overflow: ellipsis;
  flex-grow: 1;
}
```

### 🖼️ **Images responsives**
```css
.collection-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}
```

## 🎯 **Avantages de la grille 3 colonnes**

### ✅ **Présentation optimale**
- **3 produits par ligne** : Équilibre parfait entre visibilité et densité
- **Uniformité visuelle** : Toutes les cartes ont la même taille
- **Lisibilité améliorée** : Texte optimisé pour chaque taille d'écran

### ✅ **Performance**
- **Aspect ratio fixe** : Évite les recalculs de layout
- **Images optimisées** : Tailles fixes pour chaque breakpoint
- **CSS Grid natif** : Performance maximale

### ✅ **Expérience utilisateur**
- **Navigation intuitive** : Grille claire et organisée
- **Responsive fluide** : Adaptation automatique à tous les écrans
- **Accessibilité** : Structure logique pour les lecteurs d'écran

## 🛠️ **Structure technique**

### 📐 **Grid CSS**
```css
.collections-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 40px;
  max-width: 1400px;
  margin: 0 auto;
}
```

### 🎨 **Cartes flexibles**
```css
.collection-card {
  display: flex;
  flex-direction: column;
  aspect-ratio: 0.8;
  width: 100%;
}

.collection-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
```

### 📱 **Breakpoints**
```css
/* Desktop Large */
@media (min-width: 1401px) { /* 3 colonnes + */ }

/* Desktop Standard */
@media (min-width: 1200px) and (max-width: 1400px) { /* 3 colonnes */ }

/* Desktop Compact */
@media (min-width: 992px) and (max-width: 1199px) { /* 3 colonnes compactes */ }

/* Tablette */
@media (min-width: 768px) and (max-width: 991px) { /* 2 colonnes */ }

/* Mobile Large */
@media (min-width: 576px) and (max-width: 767px) { /* 2 colonnes compactes */ }

/* Mobile Small */
@media (max-width: 575px) { /* 1 colonne */ }

/* Ultra Large */
@media (min-width: 1800px) { /* 4 colonnes */ }
```

## 🎨 **Personnalisation**

### 🔧 **Modifier le nombre de colonnes**
```css
/* Pour 4 colonnes sur desktop */
.collections-grid {
  grid-template-columns: repeat(4, 1fr) !important;
}

/* Pour 2 colonnes sur desktop */
.collections-grid {
  grid-template-columns: repeat(2, 1fr) !important;
}
```

### 📏 **Ajuster l'espacement**
```css
/* Plus d'espace entre les cartes */
.collections-grid {
  gap: 60px !important;
}

/* Moins d'espace */
.collections-grid {
  gap: 20px !important;
}
```

### 📐 **Modifier l'aspect ratio**
```css
/* Cartes plus hautes */
.collection-card {
  aspect-ratio: 0.7 !important;
}

/* Cartes plus larges */
.collection-card {
  aspect-ratio: 1.2 !important;
}
```

## 📊 **Comparaison des layouts**

| Écran | Colonnes | Gap | Max Width | Image Height |
|-------|----------|-----|-----------|--------------|
| Ultra Large (>1800px) | 4 | 60px | 1800px | 320px |
| Large Desktop (>1400px) | 3 | 50px | 1600px | 320px |
| Desktop Standard (1200-1400px) | 3 | 40px | 1300px | 280px |
| Desktop Compact (992-1199px) | 3 | 30px | 1100px | 240px |
| Tablette (768-991px) | 2 | 30px | 800px | 260px |
| Mobile Large (576-767px) | 2 | 20px | 600px | 200px |
| Mobile Small (<576px) | 1 | 20px | 400px | 220px |

---

## 🎉 **Résultat**

La grille 3 colonnes offre :

✅ **Présentation optimale** des produits  
✅ **Responsive design** parfait  
✅ **Performance** maximale  
✅ **Expérience utilisateur** fluide  
✅ **Accessibilité** respectée  

La section "Découvrez nos Collections" affiche maintenant **3 produits par ligne** sur desktop avec une adaptation intelligente pour tous les appareils ! 🎯📱