<?php
$config['databases']['default']['hostname'] = '##HOSTNAME##';
$config['databases']['default']['username'] = '##USERNAME##';
$config['databases']['default']['password'] = '##PASSWORD##';
$config['databases']['default']['database'] = '##DATABASE##';
$config['databases']['default']['port'] = 3306;
$config['databases']['default']['socket'] = '##SOCKET##';
$config['databases']['default']['charset'] = 'utf8mb4';
$config['databases']['default']['type'] = 'mysql';

// To get a token, watch for the Location: header here. Grab the "access_token" parameter.
// https://www.facebook.com/dialog/oauth?client_id=464891386855067&redirect_uri=https://www.facebook.com/connect/login_success.html&scope=basic_info,email,public_profile,user_about_me,user_activities,user_birthday,user_education_history,user_friends,user_interests,user_likes,user_location,user_photos,user_relationship_details&response_type=token
$config['facebook_token'] = '##TOKEN##
$config['locale'] = 'en';
