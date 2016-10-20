<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Jobs\WhoIsCheck;
use App\Domain;

class WhoIsChecks extends Controller
{
    protected $gist_id;

    public function run()
    {
        $domains = Domain::all();

        if($domains->isEmpty())
        {
            dd('No domains!');
        }

        $this->gist_id = env('GITHUB_GIST_ID');

        foreach($domains as $domain)
        {
            $this->dispatch(new WhoIsCheck($domain, $this->gist_id));

            // Only run once on local..
            if (app('app')->environment() == 'local') break;
        }

        echo 'Whois checking scheduled.';
    }

}
