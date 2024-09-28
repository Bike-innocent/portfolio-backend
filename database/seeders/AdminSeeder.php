<?php


// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use App\Models\User;
// use Spatie\Permission\Models\Role;

// class AdminSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      *
//      * @return void
//      */
//     public function run()
//     {
//         // Create roles
//         $adminRole = Role::firstOrCreate(['name' => 'admin']);

//         // Create an admin user
//         $admin = User::firstOrCreate([
//             'email' => 'admin@gmail.com',
//         ], [
//             'name' => 'chibuike',
//             'password' => bcrypt('password'), // Change this to a secure password
//         ]);

//         // Assign the admin role to the user
//         $admin->assignRole($adminRole);
//     }
// }


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Find the user by their email
        // $adminEmail = 'onyemaobichibuikeinnocent.com@gmail.com';
        $adminEmail = 'admin@gmail.com';
        $admin = User::where('email', $adminEmail)->first();

        if ($admin) {
            // If the user exists, assign them the admin role
            $admin->assignRole($adminRole);
            $this->command->info("Admin role assigned to user with email: {$adminEmail}");
        } else {
            // If the user doesn't exist, create the user and assign the admin role
            $admin = User::create([
                'name' => 'chibuike', // Update the name if necessary
                'email' => $adminEmail,
                'password' => bcrypt('securepassword'), // Set a secure password
            ]);

            $admin->assignRole($adminRole);
            $this->command->info("Admin user created and role assigned for email: {$adminEmail}");
        }
    }
}
