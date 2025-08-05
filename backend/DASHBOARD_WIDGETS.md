# Widgets du Tableau de Bord Admin - SunuBoutique

Ce document décrit tous les widgets créés pour le tableau de bord administrateur Filament.

## 📊 Widgets Disponibles

### 1. **StatsOverview** - Vue d'ensemble des statistiques
**Emplacement :** `app/Filament/Widgets/StatsOverview.php`

**Fonctionnalités :**
- 📈 **Chiffre d'affaires total** avec évolution mensuelle
- 🛒 **Total des commandes** avec pourcentage de croissance
- 👥 **Nombre de clients** avec évolution
- 📦 **Produits actifs** et alertes de rupture de stock
- ⏳ **Commandes en attente** à traiter
- 💰 **Panier moyen** des 30 derniers jours

**Données affichées :**
- Graphiques en mini-charts pour chaque métrique
- Comparaison mois actuel vs mois précédent
- Indicateurs visuels de croissance (flèches)
- Couleurs dynamiques selon les performances

---

### 2. **RevenueChart** - Graphique des revenus
**Emplacement :** `app/Filament/Widgets/RevenueChart.php`

**Fonctionnalités :**
- 📊 Graphique en barres des revenus
- 🔄 Filtres par période : 7 jours, 30 jours, 12 mois
- 💹 Évolution temporelle du chiffre d'affaires
- 🎨 Couleurs différentes selon la période

**Données affichées :**
- Revenus quotidiens, mensuels ou annuels
- Formatage automatique en FCFA
- Interface responsive

---

### 3. **OrderStatusChart** - Répartition des statuts de commandes
**Emplacement :** `app/Filament/Widgets/OrderStatusChart.php`

**Fonctionnalités :**
- 🍩 Graphique en donut des statuts
- 🏷️ Labels en français
- 🎨 Couleurs codées par statut

**Statuts trackés :**
- 🟡 En attente (Orange)
- 🔵 En cours (Bleu)
- 🟣 Expédiée (Violet)
- 🟢 Livrée (Vert)
- 🔴 Annulée (Rouge)

---

### 4. **PaymentMethodsChart** - Méthodes de paiement
**Emplacement :** `app/Filament/Widgets/PaymentMethodsChart.php`

**Fonctionnalités :**
- 🥧 Graphique en secteurs (pie chart)
- 💳 Répartition des méthodes de paiement
- 📊 Données des commandes payées uniquement

**Méthodes trackées :**
- PayDunya (Bleu)
- Paiement à la livraison (Vert)
- Virement bancaire (Violet)
- Mobile Money (Orange)
- Carte de crédit (Rose)

---

### 5. **TopProductsTable** - Produits les plus vendus
**Emplacement :** `app/Filament/Widgets/TopProductsTable.php`

**Fonctionnalités :**
- 📋 Tableau des produits bestsellers
- 🖼️ Images des produits
- 📊 Quantités vendues et revenus générés
- ⚠️ Alertes de stock avec codes couleur
- 🔍 Recherche et tri

**Colonnes affichées :**
- Image du produit
- Nom et catégorie
- Prix unitaire
- Quantité totale vendue
- Revenus générés
- Stock restant

---

### 6. **RecentOrdersTable** - Commandes récentes
**Emplacement :** `app/Filament/Widgets/RecentOrdersTable.php`

**Fonctionnalités :**
- 📋 Liste des 50 dernières commandes
- 👤 Informations client
- 💰 Montants et statuts
- 🔗 Liens directs vers les détails
- 🔄 Actualisation automatique (30s)

**Colonnes affichées :**
- Numéro de commande
- Nom et email du client
- Montant total
- Statut de la commande
- Statut du paiement
- Méthode de paiement
- Date de création

---

### 7. **LowStockAlert** - Alertes de stock faible
**Emplacement :** `app/Filament/Widgets/LowStockAlert.php`

**Fonctionnalités :**
- ⚠️ Alerte pour produits avec stock ≤ 10
- 🚨 Mise en évidence des ruptures de stock
- 🔗 Liens directs pour modification
- 👁️ Masquage automatique si pas d'alertes

**Niveaux d'alerte :**
- 🔴 Rupture de stock (0 unités)
- 🟡 Stock très faible (1-5 unités)
- 🔵 Stock faible (6-10 unités)

---

## 🎛️ Configuration

### Ordre d'affichage
Les widgets sont ordonnés par la propriété `$sort` :
1. StatsOverview (sort: 1)
2. RevenueChart (sort: 2)
3. TopProductsTable (sort: 3)
4. RecentOrdersTable (sort: 4)
5. OrderStatusChart (sort: 5)
6. LowStockAlert (sort: 6)
7. PaymentMethodsChart (sort: 7)

### Colonnes
- **StatsOverview** : Pleine largeur (`full`)
- **RevenueChart** : Pleine largeur (`full`)
- **TopProductsTable** : Pleine largeur (`full`)
- **RecentOrdersTable** : Pleine largeur (`full`)
- **OrderStatusChart** : 1 colonne (`1`)
- **PaymentMethodsChart** : 1 colonne (`1`)
- **LowStockAlert** : 1 colonne (`1`)

### Actualisation automatique
- **TopProductsTable** : 30 secondes
- **RecentOrdersTable** : 30 secondes
- **LowStockAlert** : 60 secondes

## 🚀 Utilisation

### Accès au tableau de bord
1. Connectez-vous à l'admin Filament : `/admin`
2. Le tableau de bord s'affiche automatiquement
3. Tous les widgets se chargent avec les données en temps réel

### Interactions disponibles
- **Filtrage** : RevenueChart permet de filtrer par période
- **Recherche** : Tables permettent la recherche dans les données
- **Tri** : Colonnes triables dans les tableaux
- **Actions** : Liens directs vers les ressources Filament
- **Pagination** : Contrôle du nombre d'éléments affichés

## 🔧 Personnalisation

### Modifier les seuils d'alerte
Dans `LowStockAlert.php`, changez la condition :
```php
->where('stock_quantity', '<=', 10) // Modifier le seuil ici
```

### Ajouter de nouvelles métriques
1. Créer un nouveau widget : `php artisan make:filament-widget MonWidget`
2. L'ajouter dans `AdminPanelProvider.php`
3. Définir l'ordre avec `protected static ?int $sort = X;`

### Modifier les couleurs
Les couleurs sont définies dans chaque widget avec des codes RGBA.

## 📈 Métriques Calculées

### Chiffre d'affaires
- Basé sur les commandes avec `payment_status = 'paid'`
- Comparaison mois actuel vs mois précédent
- Graphiques des 7 derniers jours

### Croissance
- Formule : `((valeur_actuelle - valeur_précédente) / valeur_précédente) * 100`
- Affichage avec indicateurs visuels (flèches)

### Panier moyen
- Moyenne des montants des commandes payées sur 30 jours
- Formatage automatique en FCFA

Ce tableau de bord offre une vue complète et en temps réel de l'activité de votre boutique ! 🎯