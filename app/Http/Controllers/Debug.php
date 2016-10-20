<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use GrahamCampbell\Bitbucket\Facades\Bitbucket;
use GrahamCampbell\GitHub\Facades\GitHub;

class Debug extends Controller
{
    public function bitbucket()
    {
        dd(Bitbucket::api('Repositories\Repository')->get('gentlero', 'bitbucket-api'));
    }

    public function github()
    {
        //dd( GitHub::gist()->all() );
        dd( GitHub::gist()->show( '772bdb44974246662cd05b36469e77e3' ) );
    }
}
