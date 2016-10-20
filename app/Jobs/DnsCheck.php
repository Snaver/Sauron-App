<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use GuzzleHttp\Client;
use App\Domain;
use GrahamCampbell\GitHub\GitHubManager;

class DnsCheck implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $location;
    protected $domain;
    protected $type;
    protected $gist_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($location, Domain $domain, $type, $gist_id)
    {
        $this->location = $location;
        $this->domain = $domain;
        $this->type = $type;

        $this->gist_id = $gist_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Client $client, GitHubManager $github)
    {
        if ($this->attempts() > 3)
        {
            $this->delete();
        }

        try {
            $this->url = 'http://www.dns-lg.com/'.$this->location.'/'.$this->domain->domain.'/'.$this->type;
            //dump($this->url);

            $res = $client->request('GET', $this->url);

            if($res->getStatusCode() == 200)
            {
                $data = (array)json_decode( $res->getBody(), true );

                if(empty($data['answer']))
                {
                    $answers = [];
                }
                else
                {
                    $answers = array_column($data['answer'], 'rdata');
                    natsort($answers);
                    $answers = array_values($answers);
                }

                // Grab entire record
                $this->gist = $github->gist()->show( $this->gist_id );

                $original_json = $this->gist['files'][$this->domain->domain . '.dns.json']['content'];

                // Decode specific data back to array
                $new = (array)json_decode($original_json, true);

                // Replace with new data
                $new[$this->location][$this->type] = $answers;

                // Encode back to json
                $new_json = json_encode($new, JSON_PRETTY_PRINT);

                //dd($original_json, $new_json);

                if($original_json != $new_json)
                {
                    // Update format for github
                    $update['files'][$this->domain->domain . '.dns.json']['content'] = $new_json;

                    //dd($update);

                    $gist = $github->api('gists')->update($this->gist_id, $update);

                    echo $this->location.'/'.$this->domain->domain.'/'.$this->type . ' changes found - updating.'.PHP_EOL;
                }
                else
                {
                    echo $this->location.'/'.$this->domain->domain.'/'.$this->type . ' no changes found - skipping.'.PHP_EOL;
                }
            }
        } catch (Exception $ex) {
            Log::error($ex);
        }
    }
}
