<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10001',
            ],
            [
                'name' => 'Emma Johnson',
                'email' => 'emma.j@example.com',
                'phone' => '+1 (555) 987-6543',
                'address' => '456 Oak Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'country' => 'USA',
                'postal_code' => '90001',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.b@example.com',
                'phone' => '+1 (555) 456-7890',
                'address' => '789 Pine Road',
                'city' => 'Chicago',
                'state' => 'IL',
                'country' => 'USA',
                'postal_code' => '60601',
            ],
            [
                'name' => 'Sarah Davis',
                'email' => 'sarah.d@example.com',
                'phone' => '+1 (555) 234-5678',
                'address' => '321 Elm Street',
                'city' => 'Houston',
                'state' => 'TX',
                'country' => 'USA',
                'postal_code' => '77001',
            ],
            [
                'name' => 'Robert Wilson',
                'email' => 'robert.w@example.com',
                'phone' => '+44 20 7946 0958',
                'address' => '15 Oxford Street',
                'city' => 'London',
                'state' => 'London',
                'country' => 'UK',
                'postal_code' => 'W1D 1BS',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        $this->command->info('✅ 5 sample customers added successfully!');
    }
}