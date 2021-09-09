<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
        'status' => true,
        'data' => compact('users')
        ], 200);
    }

    public function block($id)
    {
        try {
        $user = User::findOrFail($id);
        } catch (ModelNotFoundException $e) {
        return response()->json([
            'status' => false,
            'message' => 'User tidak ditemukan',
            'data' => compact('e')
        ], 404);
        }

        $user->update([
        'blocked' => 1
        ]);

        return response()->json([
        'status' => true,
        'message' => 'User berhasil diblokir',
        'data' => compact('user'),
        ], 200);
    }

    public function unblock($id)
    {
        try {
        $user = User::findOrFail($id);
        } catch (ModelNotFoundException $e) {
        return response()->json([
            'status' => false,
            'message' => 'User tidak ditemukan',
            'data' => compact('e')
        ], 404);
        }

        $user->update([
        'blocked' => 0
        ]);

        return response()->json([
        'status' => true,
        'message' => 'User berhasil dibuka blokir',
        'data' => compact('user'),
        ], 200);
    }
}
