<?php
$addons = [
    "mod_eadmeeting" => [
        'handlers' => [
            'courseeadmeeting' => [
                'delegate' => 'CoreCourseModuleDelegate',
                'method' => 'mobile_course_view',
                'displaydata' => [
                    'icon' => $CFG->wwwroot  . '/mod/eadmeeting/pix/icon.png',
                    'class' => '',
                ],
            ]
        ]
    ]
];
