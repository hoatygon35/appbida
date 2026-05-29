<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ClubController extends Controller
{
    public function index()
    {
        // Lấy danh sách câu lạc bộ kèm số lượng bàn, dịch vụ và tài khoản admin quản lý
        $clubs = Club::with(['users' => function ($query) {
            $query->where('role', 'admin');
        }])->withCount(['tables', 'services'])->get();

        return view('manager.clubs', compact('clubs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        Club::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'expiry_date' => now()->addDays(30),
        ]);

        return redirect()->route('manager.clubs.index')->with('success', 'Thêm Quán/CLB thành công!');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Kiểm tra xem CLB đã có tài khoản Admin chưa
        $existingAdmin = User::where('club_id', $request->club_id)
            ->where('role', 'admin')
            ->first();

        if ($existingAdmin) {
            return redirect()->route('manager.clubs.index')->with('error', 'Quán/CLB này đã có tài khoản quản lý (Admin)!');
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'club_id' => $request->club_id,
        ]);

        return redirect()->route('manager.clubs.index')->with('success', 'Tạo tài khoản Admin thành công!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:clubs,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $club = Club::findOrFail($request->id);
        $club->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('manager.clubs.index')->with('success', 'Cập nhật thông tin Quán/CLB thành công!');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:clubs,id'],
        ]);

        $club = Club::findOrFail($request->id);
        $club->delete();

        return redirect()->route('manager.clubs.index')->with('success', 'Xóa Quán/CLB và tất cả dữ liệu liên quan thành công!');
    }

    public function updateAdmin(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:users,id'],
            'club_id' => ['required', 'exists:clubs,id'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $request->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user = User::where('role', 'admin')->findOrFail($request->id);
        
        if ($request->club_id != $user->club_id) {
            $existingAdmin = User::where('club_id', $request->club_id)
                ->where('role', 'admin')
                ->first();

            if ($existingAdmin) {
                return redirect()->route('manager.clubs.index')->with('error', 'Chi nhánh mục tiêu đã có tài khoản quản lý! Không thể chuyển.');
            }
        }
        
        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'club_id' => $request->club_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('manager.clubs.index')->with('success', 'Cập nhật thông tin Admin thành công!');
    }

    public function destroyAdmin(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:users,id'],
        ]);

        $user = User::where('role', 'admin')->findOrFail($request->id);
        $user->delete();

        return redirect()->route('manager.clubs.index')->with('success', 'Xóa tài khoản Admin chi nhánh thành công!');
    }

    public function extendExpiry(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:clubs,id'],
            'days_to_add' => ['required', 'integer', 'min:1'],
        ]);

        $club = Club::findOrFail($request->id);
        
        // Nếu đã hết hạn (hoặc chưa có expiry_date), tính từ hôm nay. 
        // Nếu còn hạn, cộng dồn vào hạn cũ.
        if (!$club->expiry_date || $club->expiry_date->isPast()) {
            $club->expiry_date = now()->addDays($request->days_to_add);
        } else {
            $club->expiry_date = $club->expiry_date->addDays($request->days_to_add);
        }
        
        $club->save();

        return redirect()->route('manager.clubs.index')->with('success', 'Đã cộng thêm ' . $request->days_to_add . ' ngày sử dụng cho Quán/CLB!');
    }
}
