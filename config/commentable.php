<?php

return [
    'censor' => [
        'break'   => false, // Throws CensorException if detect not allowed word appear
        'replace' => '***', // The word will be replace not allowed words
        'words'   => ['fuck', 'shit', 'damn'], // Not allowed words (not case-sensitive)
    ],
];
