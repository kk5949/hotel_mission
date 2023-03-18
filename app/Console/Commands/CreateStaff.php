<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateStaff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:staff {email} {password} {name=staff}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $staff = new User;
        $staff->email = $this->argument('email');
        $staff->name = $this->argument('name');
        $staff->password = Hash::make($this->argument('password'));
        $staff->type = "S";
        $staff->save();

        $this->info($staff->toJson());

        return true;
    }
}
