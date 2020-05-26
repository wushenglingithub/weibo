<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = factory(User::class)->times(50)->make();
        User::insert($users->makeVisible(['password', 'remember_token'])->toArray());

        $user = User::find(1);
        $user->name = 'admin';
        $user->email = '450176213@qq.com';
        $user->password = bcrypt('password');
        $user->is_admin = true;
        $user->save();
    }
}