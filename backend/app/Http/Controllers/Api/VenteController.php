<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vente;
use App\Models\Voiture;
use Illuminate\Http\Request;

class VenteController extends Controller
{
    public function index()
    {
        return Vente::with('voiture')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'voiture_id' => 'required|exists:voitures,id',
            'client_id' => 'required|exists:clients,id',
            'prix_vente' => 'required|numeric|min:0',
            'date_vente' => 'required|date',
        ]);

        $voiture = Voiture::findOrFail($request->voiture_id);

        if ($voiture->etat !== 'disponible') {
            return response()->json([
                'error' => 'Seules les voitures disponibles peuvent être vendues.',
            ], 400);
        }

        $vente = Vente::create($request->all());

        $voiture->etat = 'vendue';
        $voiture->save();

        return response()->json($vente, 201);
    }
}
