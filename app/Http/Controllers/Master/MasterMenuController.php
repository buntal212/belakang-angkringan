<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MasterMenuController extends Controller
{
    /**
     * Menampilkan daftar menu dengan pagination.
     */
    public function index()
    {
        $perPage = request('per_page', 3);
        $idangkringan = request('filterAngkringanId');

        $menus = MasterMenu::with('angkringan:id,name');

        // Jika ada id dan bukan kosong
        if (!empty($idangkringan) && $idangkringan !== 'all') {
            $menus->where('angkringan_id', $idangkringan);
        }

        return response()->json(
            $menus->simplePaginate($perPage)
        );
    }

    /**
     * Menyimpan menu baru.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $kodemenu = $request->kodemenu ?? null;

            $validate = $request->validate([
                'name' => 'required|string|max:255',
                'kategori' => 'nullable|string|max:255',
                'harga' => 'required|numeric|min:0',
                'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'name.required' => 'Nama menu wajib diisi.',
                'harga.required' => 'Harga wajib diisi.',
                'harga.min' => 'Harga tidak boleh kurang dari 0.',
                'gambar.image' => 'File harus berupa gambar.',
                'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            ]);

            $angkringanId = $request->user()->id;

            // generate jika create baru
            if (!$kodemenu) {
                $kodemenu = date('YmdHis') . '-' . $angkringanId;
            }

            $data = [
                'kodemenu' => $kodemenu,
                'angkringan_id' => $angkringanId,
                'name' => $validate['name'],
                'kategori' => $validate['kategori'] ?? null,
                'harga' => $validate['harga'],
            ];

            // upload gambar
            // if ($request->hasFile('gambar')) {

            //     $folder = public_path('storage/sinangkring/' . $angkringanId);

            //     if (!file_exists($folder)) {
            //         mkdir($folder, 0777, true);
            //     }

            //     $file = $request->file('gambar');
            //     $filename = time() . '_' . $file->getClientOriginalName();

            //     $file->move($folder, $filename);

            //     $data['gambar'] = 'sinangkring/' . $angkringanId . '/' . $filename;
            // }

            $menu = MasterMenu::updateOrCreate(
                [
                    'kodemenu' => $kodemenu
                ],
                $data
            );

            DB::commit();
            $result = self::getkode($kodemenu);

            return response()->json([
                'status' => 'success',
                'message' => $menu->wasRecentlyCreated
                    ? 'Menu berhasil ditambahkan.'
                    : 'Menu berhasil diupdate.',
                'data' => $result,
            ], 200);

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('STORE MENU ERROR', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan menu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $kodemenu)
    {
        DB::beginTransaction();

        try {
            $menu = MasterMenu::where('kodemenu', $kodemenu)->first();

            if (!$menu) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Menu tidak ditemukan.',
                ], 404);
            }

            $validate = $request->validate([
                'name' => 'sometimes|string|max:255',
                'kategori' => 'nullable|string|max:255',
                'harga' => 'sometimes|numeric|min:0',
                'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'name.string' => 'Nama menu harus berupa teks.',
                'harga.numeric' => 'Harga harus berupa angka.',
                'harga.min' => 'Harga tidak boleh kurang dari 0.',
                'gambar.image' => 'File harus berupa gambar.',
                'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            ]);

            if (isset($validate['name'])) $menu->name = $validate['name'];
            if (array_key_exists('kategori', $validate)) $menu->kategori = $validate['kategori'];
            if (isset($validate['harga'])) $menu->harga = $validate['harga'];

            if ($request->hasFile('gambar')) {
                $angkringanId = $menu->angkringan_id;
                $folder = public_path('storage/sinangkring/' . $angkringanId);

                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }

                // hapus gambar lama jika ada
                if ($menu->gambar) {
                    $oldPath = public_path('storage/' . $menu->gambar);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move($folder, $filename);

                $menu->gambar = 'sinangkring/' . $angkringanId . '/' . $filename;
            }

            $menu->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Menu berhasil diupdate.',
                'data' => $menu->load('angkringan:id,name'),
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('UPDATE MENU ERROR', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate menu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($kodemenu)
    {
        DB::beginTransaction();

        try {
            $menu = MasterMenu::where('kodemenu', $kodemenu)->first();

            if (!$menu) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Menu tidak ditemukan.',
                ], 404);
            }

            $menu->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Menu berhasil dihapus.',
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('DELETE MENU ERROR', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus menu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getkode($kodemenu){
        $menus = MasterMenu::with('angkringan:id,name')->where('kodemenu', $kodemenu);
        return new JsonResponse($menus);
    }
}
