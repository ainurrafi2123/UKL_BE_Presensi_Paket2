<?php

namespace App\Http\Controllers;

use App\Models\User; // Pastikan untuk menggunakan model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getUser($id = null)
    {
        if ($id) {
            // Mengambil pengguna berdasarkan ID
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan'], 404);
            }

            return response()->json($user);
        }

        // Mengambil semua data pengguna
        $dataUser = User::all();
        return response()->json($dataUser);
    }

        public function updateUser(Request $req, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update([
            'username' => $req->get('username', $user->username),
            'name' => $req->get('name', $user->name),
            'role' => $req->get('role', $user->role),
            'password' => $req->has('password') ? bcrypt($req->get('password')) : $user->password,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

        public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => "User dengan ID $id tidak ditemukan"], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus']);
    }

}

