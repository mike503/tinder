# Tinder API in PHP

Basics:

1. Copy config.sample.php to config.php and edit as needed
1. Create database, and load schema.sql into it

Working:

* _login()
* _token()
* _request()

Not tested:

* _ping()
* _like()
* _superlike()
* _pass()
* _user()
* _profile() - currently it doesn't seem to actually update the profile, like it says it should

Not implemented (yet):

* message sending
* "updates"
* "moments" (as it is deprecated)
* popular locations
* "meta"

TODO:

* if facebook_token expires, login to FB (which can be tricky based on preferences) and get a token automatically

Big ups to:

* https://github.com/olieidel/tinder-api-super/blob/master/tinder-api.js - simple breakdown
* https://gist.github.com/rtt/10403467 - good docs
* https://github.com/tarraschk/TinderAutoLike - cute
