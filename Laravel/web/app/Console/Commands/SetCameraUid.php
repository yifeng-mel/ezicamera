<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Configuration;

class SetCameraUid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:cameraUid {uid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set camera uid';

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
        $uid = $this->argument('uid');
        Configuration::updateOrCreate(
            ['key' => 'camera_uid'],
            ['value' => $uid]
        );
    }
}
