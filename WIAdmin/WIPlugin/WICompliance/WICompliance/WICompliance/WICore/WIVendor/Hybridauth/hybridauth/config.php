<?php
/**
 * Build a configuration array to pass to `Hybridauth\Hybridauth`
 *
 * Set the Authorization callback URL to https://path/to/hybridauth/examples/example_07/callback.php
 * Understandably, you need to replace 'path/to/hybridauth' with the real path to this script.
 */
$config = [
    'callback' => SOCIAL_CALLBACK_URI,
    'providers' => [

        'Google' => [
            'enabled' => GOOGLE_ENABLED,
            'keys' => [
                'id'     => GOOGLE_ID,
                'secret' => GOOGLE_SECRET,
            ],
            'scope' => 'email',
        ],

        // 'Yahoo'     => ['enabled' => true, 'keys' => [ 'key'  => '...', 'secret' => '...']],
         'Facebook'  => [ 
            'enabled' => FACEBOOK_ENABLED,
             'keys' => [
              'id'   => FACEBOOK_ID, 'secret' => FACEBOOK_SECRET
          ]
      ],
         'Twitter'   => [ 
            'enabled' => TWITTER_ENABLED,
             'keys' => [ 'key'  => TWITTER_KEY, 'secret' => TWITTER_SECRET]
         ],
        // 'Instagram' => ['enabled' => true, 'keys' => [ 'id'   => '...', 'secret' => '...']],

    ],
];
