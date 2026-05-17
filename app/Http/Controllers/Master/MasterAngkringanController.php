<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;


class MasterAngkringanController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 10);

        $query = User::query();

        $users = $query->simplePaginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'data' => $users->items(),
                'current_page' => $users->currentPage(),
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
            ]
        ]);
    }
}
