<?php

namespace App\Http\Controllers;
use App\Models\Menus;

class MenuController extends Controller
{
    public function index()
    {
        // Mengirim data menu (bisa juga dari database nanti)
        $menus = Menus::all();

        return response()->json([
            'status' => 'success',
            'data' => $menus
        ]);
    }
}
