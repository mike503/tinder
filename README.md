# Tinder API in PHP

Basics:

1. Copy config.sample.php to config.php and edit as needed
1. Create database, and load schema.sql into it
1. Use the simple wrapper functions to create your own adventure!

Working:

* _like()
* _superlike()
* _pass()
* _user() - fetches user profile from Tinder
* _login() - login wrapper
* _token() - Tinder token get/set, issues _login() as needed
* _retoken() - delete local Tinder token and issue _request() again
* _request() - HTTP request wrapper

Not tested:

* _ping() - keeps giving me HTTP 500 "invalid ping request"
* _profile() - currently it doesn't seem to actually update the profile, like it says it should

Not implemented (yet):

* message sending
    * POST /user/{uid}/matches
        * message: text
* "updates"
    * POST /updates
        * last_activity_date: string received from previous query
* "moments" (as it is deprecated)
    * POST /feed/moments
        * last_activity_date: string received from previous query
        * last_moment_id: not sure
* popular locations
    * GET /location/popular
* "meta"
    * GET /meta

TODO:

* if facebook_token expires, login to FB (which can be tricky based on preferences) and get a token automatically
* eventually replace database.php with my actual library; this is using the first attempt to port it over to PDO

Thanks to:

* https://gist.github.com/rtt/10403467 - good docs
* https://github.com/olieidel/tinder-api-super/blob/master/tinder-api.js - simple breakdown of functions
* https://github.com/tarraschk/TinderAutoLike - cute
