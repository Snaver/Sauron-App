<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\DnsChecks;
use App\Http\Controllers\WhoIsChecks;

class Checks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checks:run {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Sauron Checks';

    protected $dnscheck;
    protected $whoischeck;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DnsChecks $dnscheck, WhoIsChecks $whoischeck)
    {
        parent::__construct();

        $this->dnscheck = $dnscheck;
        $this->whoischeck = $whoischeck;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        switch ($this->argument('type')) {
            case 'dns':
                $this->dnscheck->run();
            break;
            case 'whois':
                $this->whoischeck->run();
            break;
        }
    }
}
