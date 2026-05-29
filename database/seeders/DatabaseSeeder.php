<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Club;
use App\Models\Table;
use App\Models\Service;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // 1. Tạo 1 Manager mặc định
        User::create([
            'name' => 'Hệ thống Quản lý Bida Win',
            'username' => 'vandungldc',
            'password' => bcrypt('Dung1981'),
            'role' => 'manager',
            'club_id' => null
        ]);

        // 2. Tạo 2 chi nhánh CLB
        $club1 = Club::create(['name' => 'Bida Win Ea Rốk (Trụ sở)', 'phone' => '0795.778.778', 'address' => 'Thôn 7, Ea Rốk, Đắk Lắk']);
        $club2 = Club::create(['name' => 'Bida Win Buôn Ma Thuột', 'phone' => '0795.112.233', 'address' => '10 Nguyễn Trãi, Buôn Ma Thuột']);

        $clubs = [$club1, $club2];

        foreach ($clubs as $index => $club) {
            $n = $index + 1;
            // Tạo 1 Admin cho chi nhánh
            User::create([
                'name' => "Quản trị viên Chi nhánh $n",
                'username' => "admin$n",
                'password' => bcrypt('password'),
                'role' => 'admin',
                'club_id' => $club->id
            ]);

            // Tạo 2 nhân viên (User) cho chi nhánh
            User::create([
                'name' => "Nhân viên ca A CLB $n",
                'username' => "user{$n}a",
                'password' => bcrypt('password'),
                'role' => 'user',
                'club_id' => $club->id
            ]);
            User::create([
                'name' => "Nhân viên ca B CLB $n",
                'username' => "user{$n}b",
                'password' => bcrypt('password'),
                'role' => 'user',
                'club_id' => $club->id
            ]);

            // Tạo 5 bàn bida đơn giá thuê/giờ khác nhau
            for ($i = 1; $i <= 5; $i++) {
                Table::create([
                    'club_id' => $club->id,
                    'name' => "Bàn $i (Khu $n)",
                    'price_per_hour' => 30000 + ($i * 5000)
                ]);
            }

            // Tạo dịch vụ mẫu phân loại
            $drinks = [
                ['name' => 'Coca Cola', 'price' => 15000],
                ['name' => 'Café đen đá', 'price' => 15000],
                ['name' => 'Bò húc Thái', 'price' => 20000],
                ['name' => 'Café sữa', 'price' => 18000],
                ['name' => 'Sting dâu', 'price' => 15000],
                ['name' => 'Trà chanh', 'price' => 12000]
            ];
            foreach ($drinks as $d) {
                Service::create(['club_id' => $club->id, 'name' => $d['name'], 'price' => $d['price'], 'category' => 'thức uống']);
            }

            $foods = [
                ['name' => 'Mì tôm trứng', 'price' => 25000],
                ['name' => 'Đậu phộng rang', 'price' => 15000],
                ['name' => 'Bánh mì pate', 'price' => 20000]
            ];
            foreach ($foods as $f) {
                Service::create(['club_id' => $club->id, 'name' => $f['name'], 'price' => $f['price'], 'category' => 'đồ ăn']);
            }

            $others = [
                ['name' => 'Khăn lạnh', 'price' => 3000],
                ['name' => 'Bao thuốc 555', 'price' => 35000]
            ];
            foreach ($others as $o) {
                Service::create(['club_id' => $club->id, 'name' => $o['name'], 'price' => $o['price'], 'category' => 'khác']);
            }
        }
    }
}
