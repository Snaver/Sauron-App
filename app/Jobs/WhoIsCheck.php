<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Domain;
use Unirest;
use GrahamCampbell\GitHub\GitHubManager;

class WhoIsCheck implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $domain;
    protected $gist_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Domain $domain, $gist_id)
    {
        $this->domain = $domain;
        $this->gist_id = $gist_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GitHubManager $github)
    {
        if ($this->attempts() > 3)
        {
            $this->delete();
        }

        try {
            $headers = array("Accept" => "application/json");
            $url = "https://jsonwhoisapi.com/api/v1/whois?identifier=" . $this->domain->domain;

            Unirest\Request::verifyPeer(false);
            Unirest\Request::auth(44720, 'K8sVFgYBA1wblmuVu80CvY1rydk57yFy');

            $response = Unirest\Request::get($url, $headers);

            if($response)
            {
                // Grab entire record
                $this->gist = $github->gist()->show( $this->gist_id );

                $original_json = $this->gist['files'][$this->domain->domain . '.whois.json']['content'];

                // Encode back to json
                $new_json = json_encode($response->body, JSON_PRETTY_PRINT);

                //dd($original_json, $new_json);

                if($original_json != $new_json)
                {
                    // Update format for github
                    $update['files'][$this->domain->domain . '.whois.json']['content'] = $new_json;

                    //dd($update);

                    $gist = $github->api('gists')->update($this->gist_id, $update);

                    echo $this->domain->domain . ' changes found - updating.'.PHP_EOL;
                }
                else
                {
                    echo $this->domain->domain .' no changes found - skipping.'.PHP_EOL;
                }
            }
        } catch (Exception $ex) {
            Log::error($ex);
        }
    }
}
