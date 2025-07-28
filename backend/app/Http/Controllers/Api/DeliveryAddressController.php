<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAddress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DeliveryAddressController extends Controller
{
    /**
     * Get all delivery addresses for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->deliveryAddresses()->orderBy('is_default', 'desc')->get();

        return response()->json([
            'addresses' => $addresses
        ]);
    }

    /**
     * Store a new delivery address
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Si cette adresse est définie comme par défaut, retirer le statut des autres
        if ($request->is_default) {
            $user->deliveryAddresses()->update(['is_default' => false]);
        }

        $address = $user->deliveryAddresses()->create($request->all());

        return response()->json([
            'message' => 'Adresse ajoutée avec succès',
            'address' => $address
        ], 201);
    }

    /**
     * Update a delivery address
     */
    public function update(Request $request, DeliveryAddress $address): JsonResponse
    {
        // Vérifier que l'adresse appartient à l'utilisateur connecté
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Si cette adresse est définie comme par défaut, retirer le statut des autres
        if ($request->is_default) {
            $request->user()->deliveryAddresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($request->all());

        return response()->json([
            'message' => 'Adresse mise à jour avec succès',
            'address' => $address
        ]);
    }

    /**
     * Delete a delivery address
     */
    public function destroy(Request $request, DeliveryAddress $address): JsonResponse
    {
        // Vérifier que l'adresse appartient à l'utilisateur connecté
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $address->delete();

        return response()->json([
            'message' => 'Adresse supprimée avec succès'
        ]);
    }

    /**
     * Set an address as default
     */
    public function setDefault(Request $request, DeliveryAddress $address): JsonResponse
    {
        // Vérifier que l'adresse appartient à l'utilisateur connecté
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        // Retirer le statut par défaut des autres adresses
        $request->user()->deliveryAddresses()
            ->where('id', '!=', $address->id)
            ->update(['is_default' => false]);

        // Définir cette adresse comme par défaut
        $address->update(['is_default' => true]);

        return response()->json([
            'message' => 'Adresse par défaut mise à jour',
            'address' => $address
        ]);
    }
}