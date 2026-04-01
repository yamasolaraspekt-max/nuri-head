<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ===== Config =====
            $email    = 'sadid.ramin2@gmail.com';
            $password = 'Ramo@12341';

            // item_id values from your <select>
            $items = [
                'Administrator',
                'Employee',
                'Customer',
                'Problem',
                'Product',
                'Error',
                'Users',
                'Comment',
                'Problems',
                'Invoice',
                'Programmer',
                'Partner',
                'Admin',
                'Email',
                'Inquiry',
                'Service',
                'Maintenance',
                'Organization',
                'Finance',
                'Super',
            ];

            // ===== Create employee =====
            $employeeId = DB::table('employees')->insertGetId([
                'title'            => 'Mr',
                'name'             => 'Ramin',
                'lastname'         => 'Sadid',
                'email'            => $email,
                'daily_start_time' => '07:30:00',
                'daily_end_time'   => '16:00:00',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // ===== Create user =====
            $userId = DB::table('users')->insertGetId([
                'name'              => (string) $employeeId, // store employee_id in users.name
                'email'             => $email,
                'email_verified_at' => now(),
                'password'          => Hash::make($password),
                'remember_token'    => Str::random(10),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // ===== Create user_rolls for all items =====
            $now = now();

            $rows = array_map(function ($item) use ($userId, $now) {
                return [
                    'user_id'    => $userId,
                    'item_id'    => $item,
                    'is_read'    => 'on',
                    'is_update'  => 'on',
                    'is_delete'  => 'on',
                    'is_add'     => 'on',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $items);

            DB::table('user_rolls')->insert($rows);
        });
    }
}
