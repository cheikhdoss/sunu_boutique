<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📊 Générateur de Rapports Avancés
        </x-slot>

        <x-slot name="description">
            Générez des rapports détaillés en PDF ou Excel pour analyser vos performances
        </x-slot>

        <div class="space-y-6">
            <!-- Formulaire de sélection -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{ $this->form }}
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-wrap gap-3 justify-center">
                <x-filament::button
                    wire:click="generatePDF"
                    color="danger"
                    icon="heroicon-o-document-text"
                    size="lg"
                >
                    📄 Générer PDF
                </x-filament::button>

                <x-filament::button
                    wire:click="generateExcel"
                    color="success"
                    icon="heroicon-o-table-cells"
                    size="lg"
                >
                    📊 Générer Excel
                </x-filament::button>

                <x-filament::button
                    wire:click="viewReport"
                    color="info"
                    icon="heroicon-o-eye"
                    size="lg"
                >
                    👁️ Voir Données JSON
                </x-filament::button>
            </div>

            <!-- Informations sur les rapports -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center space-x-3">
                        <div class="text-2xl">📊</div>
                        <div>
                            <h3 class="font-semibold text-blue-900">Rapport de Ventes</h3>
                            <p class="text-sm text-blue-700">Chiffre d'affaires, évolution, top produits</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center space-x-3">
                        <div class="text-2xl">📦</div>
                        <div>
                            <h3 class="font-semibold text-green-900">Performance Produits</h3>
                            <p class="text-sm text-green-700">Rotation stock, rentabilité, catégories</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center space-x-3">
                        <div class="text-2xl">👥</div>
                        <div>
                            <h3 class="font-semibold text-purple-900">Comportement Clients</h3>
                            <p class="text-sm text-purple-700">Segmentation, CLV, rétention</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métriques rapides -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3">📈 Métriques Incluses</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="space-y-1">
                        <div class="font-medium text-gray-700">Ventes</div>
                        <ul class="text-gray-600 space-y-1">
                            <li>• Chiffre d'affaires</li>
                            <li>• Évolution temporelle</li>
                            <li>• Panier moyen</li>
                        </ul>
                    </div>
                    <div class="space-y-1">
                        <div class="font-medium text-gray-700">Produits</div>
                        <ul class="text-gray-600 space-y-1">
                            <li>• Top ventes</li>
                            <li>• Rotation stock</li>
                            <li>• Rentabilité</li>
                        </ul>
                    </div>
                    <div class="space-y-1">
                        <div class="font-medium text-gray-700">Clients</div>
                        <ul class="text-gray-600 space-y-1">
                            <li>• Segmentation RFM</li>
                            <li>• Valeur vie client</li>
                            <li>• Rétention</li>
                        </ul>
                    </div>
                    <div class="space-y-1">
                        <div class="font-medium text-gray-700">Tendances</div>
                        <ul class="text-gray-600 space-y-1">
                            <li>• Croissance</li>
                            <li>• Saisonnalité</li>
                            <li>• Prédictions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-url', (event) => {
                window.open(event.url, '_blank');
            });
        });
    </script>
</x-filament-widgets::widget>