# Sauron
Personal Laravel 5.3 project to monitor, notify and record changes in Whois and DNS records. Built to get up to speed with new Laravel technology and play with the free tier provided by Heroku.

Utilising [Laravel Queues & Jobs](https://laravel.com/docs/5.3/queues), checks can be performed on a set of defined domains for either DNS or Whois records. The [Laravel Scheduler](https://laravel.com/docs/5.3/scheduling) can be used to arrange when these checks are run.

Alternatively a new set of [Laravel artisan commands](https://laravel.com/docs/5.3/artisan#writing-commands) are provided to run these checks `php artisan checks:run dns` and `php artisan checks:run whois`.

Both checks return JSON results which are stored as text diffs in a single [GitHub Gist](https://gist.github.com/), by storing them this way; instead of say a database it prevents data duplication and allows you to see the historical differences over time.

Primarily built for running on the [Heroku platform](https://www.heroku.com), you can run this project quite happily using the free plan. The provided Procfile will automatically configure a Web and Worker Dyno. Alternatively this project should run out of the box on [Laravel Forge](https://forge.laravel.com/) as well.

[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy)

# Install & Setup

* Run `./heroku_init.sh` or use above Heroku deploy button
* Set heroku custom config vars - `SAURON_EMAIL`, `SAURON_JSONWHOISAPI_API_KEY` and `SAURON_JSONWHOISAPI_CUSTOMER_ID` (https://jsonwhoisapi.com/register), `GITHUB_TOKEN` (https://github.com/settings/tokens") and `SAURON_GITHUB_GIST_ID` (https://gist.github.com/)
* Setup Scheduler / Jobs
  * Wake up Heroku dyno if running on the free plan - Daily `curl https://${HEROKU_APP_NAME}`
  * Run DNS Checks - Daily `php artisan checks:run dns`
  * Run Whois Checks - Daily `php artisan checks:run whois`

# TODO

* Add notifications upon changes - email, slack, webhook
* Create interface for storing records, so either github, bitbucket, text file could be used
* Add options for using different providers, currently using dns-lg.com for DNS records and jsonwhoisapi.com for Whois checks
* Create Laravel/Unit tests
