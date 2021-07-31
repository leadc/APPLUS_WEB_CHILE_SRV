<?php

namespace App\Console\Commands;

use App\Models\Token;
use Illuminate\Console\Command;

class CleanDataBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans expired web tokens from database';

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
        Token::CleanOldTokens();
        return 0;
    }
}
