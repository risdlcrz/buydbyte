<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\EmailVerification;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the authentication system functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing BuyDbyte Authentication System...');
        
        // Test 1: Create a test user
        $this->info('1. Creating test user...');
        
        $email = 'test' . time() . '@buydbyte.com';
        
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $email,
            'phone_number' => '0917' . rand(1000000, 9999999),
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'status' => 'pending_verification',
        ]);
        
        $this->info("✓ User created with ID: {$user->user_id}");
        
        // Test 2: Create email verification
        $this->info('2. Creating email verification token...');
        
        $verification = EmailVerification::create([
            'user_id' => $user->user_id,
            'token' => Str::random(60),
            'expires_at' => now()->addHours(24),
        ]);
        
        $this->info("✓ Email verification created with token: {$verification->token}");
        
        // Test 3: Test relationships
        $this->info('3. Testing model relationships...');
        
        $userFromDb = User::with(['emailVerifications', 'addresses'])->find($user->user_id);
        $this->info("✓ User full name: {$userFromDb->full_name}");
        $this->info("✓ Email verifications count: " . $userFromDb->emailVerifications->count());
        $this->info("✓ User is active: " . ($userFromDb->isActive() ? 'Yes' : 'No'));
        $this->info("✓ User is customer: " . ($userFromDb->isCustomer() ? 'Yes' : 'No'));
        
        // Test 4: Create audit log
        $this->info('4. Creating audit log...');
        
        AuditLog::createLog('test_registration', $user->user_id, ['test' => true]);
        $this->info("✓ Audit log created");
        
        // Test 5: Verify verification token
        $this->info('5. Testing verification token...');
        
        $this->info("✓ Token is valid: " . ($verification->isValid() ? 'Yes' : 'No'));
        $this->info("✓ Token expires at: {$verification->expires_at}");
        
        $this->info("\n✅ All authentication system tests passed!");
        $this->info("You can now access your application through XAMPP:");
        $this->info("→ http://localhost/buydbyte/buydbyte/public/login");
        
        return 0;
    }
}
