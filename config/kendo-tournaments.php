<?php

return [


    'default_settings' => [
        'fightingAreas'        => '1',
        'fightDuration'        => '05:00',
        'hasPreliminary'       => '1',
        'preliminaryGroupSize' => '3',
        'preliminaryDuration'  => '05:00',
        'preliminaryWinner'    => '1',
        'hasEncho'             => '1',
        'enchoQty'             => '1',
        'enchoDuration'        => '0',
        'hasHantei'            => '0',
        'hanteiLimit'          => '0', // 1/2 Finals
        'enchoGoldPoint'       => '0', // Step where Encho has no more time limit
        'limitByEntity'        => '4',
        'cost'                 => '',
        'treeType'             => '1',
        'seedQuantity'         => '4',

    ],

    'MIN_COMPETITORS_X_AREA' => 2,

    'ROUND_ROBIN'        => 0,
    'DIRECT_ELIMINATION' => 1,

];
