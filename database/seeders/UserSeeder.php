<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminprovrole = Role::create(['name' => 'adminprov']);
        $adminkabrole = Role::create(['name' => 'adminkab']);
        $pclrole = Role::create(['name' => 'pcl']);
        $pmlrole = Role::create(['name' => 'pml']);

        $adminprov = User::create([
            'name' => 'Admin Provinsi',
            'email' => 'adminprov@gmail.com',
            'password' => bcrypt('horehore')
        ]);
        $adminprov->assignRole('adminprov');

        $adminkab = User::create([
            'name' => 'Admin Kabupaten',
            'email' => 'admin01@gmail.com',
            'password' => bcrypt('horehore'),
            'regency_id' => 1
        ]);
        $adminkab->assignRole('adminkab');

        $pcl = User::create([
            'name' => 'PCL Pacitan',
            'email' => 'pcl01@gmail.com',
            'password' => bcrypt('horehore'),
            'regency_id' => 1
        ]);
        $pcl->assignRole('pcl');

        $adminkab = User::create([
            'name' => 'Admin Kabupaten',
            'email' => 'admin02@gmail.com',
            'password' => bcrypt('horehore'),
            'regency_id' => 2
        ]);
        $adminkab->assignRole('adminkab');

        $pcl = User::create([
            'name' => 'PCL Pacitan',
            'email' => 'pcl02@gmail.com',
            'password' => bcrypt('horehore'),
            'regency_id' => 2
        ]);
        $pcl->assignRole('pcl');
    }
}
