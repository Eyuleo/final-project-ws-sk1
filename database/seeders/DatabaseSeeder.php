<?php

namespace Database\Seeders;

use App\Models\ClientProfile;
use App\Models\Order;
use App\Models\Review;
use App\Models\ServiceListing;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Seeding roles and permissions...');
        $this->call(RoleSeeder::class);

        $this->command->info('Seeding categories...');
        $this->call(CategorySeeder::class);

        $this->command->info('Creating test users...');

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@studentskills.et'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create test student users with profiles
        $this->command->info('Creating student users...');
        for ($i = 1; $i <= 10; $i++) {
            $student = User::firstOrCreate(
                ['email' => "student$i@example.com"],
                [
                    'name' => "Student User $i",
                    'password' => Hash::make('password'),
                    'role' => 'student',
                ]
            );
            
            if (!$student->hasRole('student')) {
                $student->assignRole('student');
            }

            $studentProfile = StudentProfile::firstOrCreate(
                ['user_id' => $student->id],
                StudentProfile::factory()->raw(['user_id' => $student->id])
            );

            // Create 2-5 service listings per student if none exist
            if ($studentProfile->serviceListings()->count() === 0) {
                $listingCount = rand(2, 5);
                for ($j = 0; $j < $listingCount; $j++) {
                    ServiceListing::factory()->create([
                        'student_profile_id' => $studentProfile->id,
                        'status' => rand(0, 10) > 2 ? 'active' : 'draft', // 80% active
                    ]);
                }
            }
        }

        // Create test client users with profiles
        $this->command->info('Creating client users...');
        for ($i = 1; $i <= 5; $i++) {
            $client = User::firstOrCreate(
                ['email' => "client$i@example.com"],
                [
                    'name' => "Client User $i",
                    'password' => Hash::make('password'),
                    'role' => 'client',
                ]
            );
            
            if (!$client->hasRole('client')) {
                $client->assignRole('client');
            }

            ClientProfile::firstOrCreate(
                ['user_id' => $client->id],
                ClientProfile::factory()->raw(['user_id' => $client->id])
            );
        }

        // Create some orders with various statuses
        $this->command->info('Creating sample orders...');
        $students = StudentProfile::with('user')->get();
        $clients = ClientProfile::with('user')->get();

        foreach ($clients as $client) {
            // Each client places 2-4 orders
            $orderCount = rand(2, 4);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $student = $students->random();
                $serviceListing = $student->serviceListings()->inRandomOrder()->first();
                
                if (!$serviceListing) {
                    continue;
                }

                // Random order status
                $statusRand = rand(1, 10);
                $orderFactory = Order::factory();

                if ($statusRand <= 2) {
                    $order = $orderFactory->create([
                        'client_profile_id' => $client->id,
                        'student_profile_id' => $student->id,
                        'service_listing_id' => $serviceListing->id,
                    ]);
                } elseif ($statusRand <= 4) {
                    $order = $orderFactory->accepted()->create([
                        'client_profile_id' => $client->id,
                        'student_profile_id' => $student->id,
                        'service_listing_id' => $serviceListing->id,
                    ]);
                } elseif ($statusRand <= 6) {
                    $order = $orderFactory->inProgress()->create([
                        'client_profile_id' => $client->id,
                        'student_profile_id' => $student->id,
                        'service_listing_id' => $serviceListing->id,
                    ]);
                } elseif ($statusRand <= 8) {
                    $order = $orderFactory->completed()->create([
                        'client_profile_id' => $client->id,
                        'student_profile_id' => $student->id,
                        'service_listing_id' => $serviceListing->id,
                    ]);
                } else {
                    $order = $orderFactory->approved()->create([
                        'client_profile_id' => $client->id,
                        'student_profile_id' => $student->id,
                        'service_listing_id' => $serviceListing->id,
                    ]);

                    // Create review for approved orders
                    Review::factory()->create([
                        'order_id' => $order->id,
                        'reviewer_id' => $client->user_id,
                        'reviewed_id' => $student->user_id,
                    ]);
                }
            }
        }

        $this->command->info('Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('Test Credentials:');
        $this->command->info('Admin: admin@studentskills.et / password');
        $this->command->info('Students: student1@example.com to student10@example.com / password');
        $this->command->info('Clients: client1@example.com to client5@example.com / password');
    }
}
