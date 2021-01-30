<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user {username} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $username = $this->argument('username');
        $password = $this->argument('password');
        \DB::table('users')->truncate();
        \DB::table('users')->insert([
            'name' => $username,
            'email' => $username,
            'password' => bcrypt($password),
        ]);
    }
}
