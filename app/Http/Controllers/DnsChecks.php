<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Jobs\DnsCheck;
use App\Domain;
use GrahamCampbell\GitHub\GitHubManager;

class DnsChecks extends Controller
{
    protected $gist_id;

    /**
     * List of records to check
     *
     * @var array
     */
    protected $records = [
        'a',        // Get Host Address (A records)
        'cname',    // Get Canonical Name (CNAME records)
        'mx',       // Get Mail Exchange record (MX records)
        'ns',       // Get Name Servers (NS records)
        'spf',      // Get Sender Policy Framework (SPF records)
        'txt',      // Get Text record (TXT records)
    ];

    /**
     * http://www.dns-lg.com/
     *
     * @var array
     */
    protected $locations = [
        [
            'location' => 'China',
            'node' => 'cn01',
            'operator' => 'CNNIC',
        ],
        [
            'location' => 'Germany',
            'node' => 'de01',
            'operator' => 'Chaos Computer Club',
        ],
        [
            'location' => 'Netherlands',
            'node' => 'nl01',
            'operator' => 'StatDNS',
        ],
        [
            'location' => 'Switzerland',
            'node' => 'ch01',
            'operator' => 'Swiss Privacy Foundation',
        ],
        [
            'location' => 'United States',
            'node' => 'us01',
            'operator' => 'DNS-OARC',
        ],
    ];

    /**
     * Setup each DNS jobs and queue them
     *
     */
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
            foreach($this->records as $type)
            {
                foreach($this->locations as $location)
                {
                    $this->dispatch(new DnsCheck($location['node'], $domain, $type, $this->gist_id));
                }
            }

            // Only run once on local..
            if (app('app')->environment() == 'local') break;
        }

        echo 'DNS record checking scheduled.';
    }

    /**
     * Creates a blank record
     *
     */
    public function gist(GitHubManager $github)
    {
        $domains = Domain::all();

        if($domains->isEmpty())
        {
            dd('No domains!');
        }

        $template = array();
        foreach($this->locations as $k => $location)
        {
            foreach($this->records as $type)
            {
                $template[$location['node']][$type] = [];
            }
        }

        //dump($template, json_encode($template));

        $data['files'] = '';
        foreach($domains as $domain)
        {
            $data['files'][$domain->domain . '.dns.json']['content'] = json_encode($template, JSON_PRETTY_PRINT);

            $data['files'][$domain->domain . '.whois.json']['content'] = json_encode([], JSON_PRETTY_PRINT);
        }

        //dd($data);

        $gist = $github->api('gists')->update($this->gist_id, $data);

        dd($gist);
    }
}
