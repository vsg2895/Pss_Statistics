<?php

return [
    'liveagent_api_key' => env('LIVEAGENT_API_KEY', 'z1qkgpzx5809fcaua50sg7gqklqn5zub'),
    'servit_api_key' => env('SERVIT_API_KEY', 'UkVTU1RBOkFUTE45WU5D'),
    'chatMessage_api_key' => env('CHAT_MESSAGES_API_KEY', 'rug0coicui1ok6dbzgpbzigv06dvomiz'),
    'chatMessage_header_apiKey' => env('CHAT_MESSAGES_HEADER_API_KEY', 'z1qkgpzx5809fcaua50sg7gqklqn5zub'),
    'servit_url' => env('SERVIT_URL', 'https://gcm.servit.se/RestAPI/V1'),
    'liveagent_url' => env('LIVEAGENT_URL', 'https://psservice.liveagent.se/api/v3'),
//    'chatMessage_url' => env('LIVEAGENT_URL', 'https://psservice.liveagent.se/api/v3'),   /00q553aa/messages?apikey=rug0coicui1ok6dbzgpbzigv06dvomiz
    'chatMessage_url' => env('CHAT_MESSAGE_URL', 'https://psservice.liveagent.se/api/conversations'),
    'tele_two_api' => env('TELETWO_URL', 'https://tele2vaxel.se/api'),
    'tele_two_api_token' => env('TELETWO_TOKEN', 't=5938.VDo3NTg0NTVkZmRlYWFmNGIw'),
    'tele_two_api_middle_part' => env('TELETWO_MIDDLE_PART', '/contacts/info/soderbergsbil.se'),
    'gcm' => [
        'host' => env('GCM_HOST', 'gcm.servit.se'),
        'port' => env('GCM_PORT', '26'),
        'username' => env('GCM_USERNAME', 'gcmcdr'),
        'password' => env('GCM_PASSWORD', '6q2hAJGq'),
    ]
];
