<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Obtenir le profil de l'utilisateur
     */
    public function getProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'date_of_birth' => $user->date_of_birth,
                    'gender' => $user->gender,
                    'avatar' => $user->avatar,
                    'is_admin' => $user->is_admin,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour le profil de l'utilisateur
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'phone' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date|before:today',
                'gender' => 'nullable|in:male,female,other',
            ], [
                'name.required' => 'Le nom est obligatoire.',
                'email.required' => 'L\'email est obligatoire.',
                'email.email' => 'L\'email doit être valide.',
                'email.unique' => 'Cet email est déjà utilisé.',
                'date_of_birth.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
                'gender.in' => 'Le genre doit être male, female ou other.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update($request->only([
                'name',
                'email',
                'phone',
                'date_of_birth',
                'gender'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'date_of_birth' => $user->date_of_birth,
                    'gender' => $user->gender,
                    'avatar' => $user->avatar,
                    'is_admin' => $user->is_admin,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ], [
                'current_password.required' => 'Le mot de passe actuel est obligatoire.',
                'new_password.required' => 'Le nouveau mot de passe est obligatoire.',
                'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
                'new_password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe actuel est incorrect'
                ], 400);
            }

            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de mot de passe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload de l'avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            ], [
                'avatar.required' => 'L\'image est obligatoire.',
                'avatar.image' => 'Le fichier doit être une image.',
                'avatar.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif.',
                'avatar.max' => 'L\'image ne doit pas dépasser 5MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Supprimer l'ancien avatar s'il existe
            if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            // Stocker la nouvelle image
            $file = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('avatars', $filename, 'public');

            // Mettre à jour l'utilisateur
            $user->update(['avatar' => $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar mis à jour avec succès',
                'avatar_url' => url('storage/avatars/' . $filename)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload de l\'avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer l'avatar
     */
    public function removeAvatar(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Supprimer le fichier s'il existe
            if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            // Mettre à jour l'utilisateur
            $user->update(['avatar' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer le compte utilisateur
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
            ], [
                'password.required' => 'Le mot de passe est obligatoire pour supprimer le compte.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreurs de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Vérifier le mot de passe
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect'
                ], 400);
            }

            // Supprimer l'avatar s'il existe
            if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            // Supprimer tous les tokens
            $user->tokens()->delete();

            // Supprimer l'utilisateur
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Compte supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du compte',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques de l'utilisateur
     */
    public function getUserStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Pour l'instant, retourner des statistiques fictives
            // À remplacer par de vraies données quand les modèles Order, etc. seront créés
            $stats = [
                'total_orders' => 0,
                'total_spent' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0,
                'favorite_products' => 0,
                'addresses_count' => 0,
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les commandes de l'utilisateur
     */
    public function getUserOrders(Request $request): JsonResponse
    {
        try {
            // Pour l'instant, retourner un tableau vide
            // À remplacer par de vraies données quand le modèle Order sera créé
            $orders = [];

            return response()->json([
                'success' => true,
                'orders' => $orders
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des commandes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les adresses de l'utilisateur
     */
    public function getAddresses(Request $request): JsonResponse
    {
        try {
            // Pour l'instant, retourner un tableau vide
            // À remplacer par de vraies données quand le modèle Address sera créé
            $addresses = [];

            return response()->json([
                'success' => true,
                'addresses' => $addresses
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des adresses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les favoris de l'utilisateur
     */
    public function getUserFavorites(Request $request): JsonResponse
    {
        try {
            // Pour l'instant, retourner un tableau vide
            // À remplacer par de vraies données quand le modèle UserFavorite sera créé
            $favorites = [];

            return response()->json([
                'success' => true,
                'favorites' => $favorites
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des favoris',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}