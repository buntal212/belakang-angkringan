<?php

namespace App\Http\Controllers;
use App\Models\Menus;

class MenuController extends Controller
{
    public function index()
    {
        if(request('user') == 1 ){
            $menus = Menus::all();
        }else{
            $menus = Menus::where('flaging','1')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $menus
        ]);
    }
}
