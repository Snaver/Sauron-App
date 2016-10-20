# Sauron
Laravel 5.3 project to monitor changes in Whois, DNS records.

Utilising [Laravel Queues & Jobs](https://laravel.com/docs/5.3/queues) checks can be performed on a set of defined domains for either DNS or Whois records. The [Laravel Scheduler](https://laravel.com/docs/5.3/scheduling) can be used to arrange when these checks are run.

Both checks return JSON results which are stored as text diffs in a single [GitHub Gist](https://gist.github.com/), by storing them this way instead of say a database it prevents data duplication and allows you to see the historical differences over time.

Primarily built for running on the [Heroku platform](https://www.heroku.com), you can run this project quite happily using the free plan. Alternatively this project should run out of the box on [Laravel Forge](https://forge.laravel.com/) as well.

# TODO

* Add notifications upon changes - email, slack, webhook
* Create interface for storing records, so either github, bitbucket, text file could be used
* Add options for using different providers, currently using dns-lg.com for DNS records and jsonwhoisapi.com for Whois checks
* Create Laravel/Unit tests