<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParentUserController extends Controller
{
    public function index(Request $request)
    {
        // Hanya ambil user dengan role 'parent'
        $query = User::where('role', 'parent');

        // Search berdasarkan Nama, NIK, No Telp, atau Email
        $query->when($request->search, function ($q, $search) {
            return $q->where(function($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });

        $parents = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.parents.index', compact('parents'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|unique:users,phone_number,' . $id,
            'relationship' => 'required|in:Ayah,Ibu,Wali',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'phone_number', 'relationship', 'email']));

        return back()->with('success', 'Data orang tua berhasil diperbarui.');
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate(['password' => 'required|min:8|confirmed']);
        
        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password akun ' . $user->name . ' berhasil diganti.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun orang tua berhasil dihapus dari sistem.');
    }
}