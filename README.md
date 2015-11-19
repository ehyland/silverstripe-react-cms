# Summary
[SilverStripe](https://www.silverstripe.org/) based CMS for my [portfolio web app](https://github.com/ehyland/eamon-app).

# Dependencies
- [composer](https://getcomposer.org/)
- [HHVM](http://hhvm.com/) or [php 5.6](http://www.php.net/)

# Install
1. `composer install`
2.  In a web browser, navigate to `dev/build/?flush=all`

# API endpoints
* api/data - site data
* api/ - page data

Add pageURL as a get variable to fetch page data. 
For example: *api/?pageURL=about-me* will return data for the about-me page
