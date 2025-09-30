<?php
return [
    'common' => [
        'actions' => [
            'back' => 'Back',
            'cancel' => 'Cancel',
            'delete' => 'Delete',
            'edit' => 'Edit',
            'filter' => 'Filter',
        ],
        'filters' => [
            'all_statuses' => 'All statuses',
        ],
        'table' => [
            'no_records' => 'No records found',
        ],
        'time' => [
            'minutes_short' => 'min',
        ],
        'forms' => [
            'characters' => 'characters',
        ],
    ],
    'admin' => [
        'content' => [
            'status' => [
                'draft' => 'Draft',
                'published' => 'Published',
                'archived' => 'Archived',
                'unknown' => 'Unknown',
            ],
            'index' => [
                'title' => 'Awareness Content Management',
                'subtitle' => 'Add and edit awareness materials on the platform',
                'buttons' => [
                    'create' => 'Add New Content',
                ],
                'filters' => [
                    'search_placeholder' => 'Search content...',
                    'type_placeholder' => 'Type (article/video/pdf)',
                ],
                'table' => [
                    'headers' => [
                        'title' => 'Content Title',
                        'type' => 'Type',
                        'points' => 'Points',
                        'duration' => 'Duration',
                        'status' => 'Status',
                        'actions' => 'Actions',
                    ],
                ],
                'confirm_delete' => 'Delete this content?',
            ],
            'create' => [
                'title' => 'Create Content',
                'subtitle' => 'Add awareness materials for the organization',
                'buttons' => [
                    'submit' => 'Publish Content',
                ],
                'console' => [
                    'error_code' => 'Error code',
                    'creation_errors' => 'Content creation errors',
                    'dev_details_title' => 'Technical details (local debugging only)',
                    'type_label' => 'Type:',
                    'message_label' => 'Message:',
                    'sqlstate_label' => 'SQLSTATE:',
                    'driver_label' => 'Driver:',
                    'time_label' => 'Time:',
                ],
                'form' => [
                    'title_label' => 'Content title',
                    'title_placeholder' => 'Example: Top password security practices',
                    'category_label' => 'Category',
                    'category_aria' => 'Select category',
                    'category_placeholder' => 'Choose a category',
                    'default_categories' => [
                        'basic_security' => 'Basic protection',
                        'email_security' => 'Email security',
                        'mobile_security' => 'Mobile device protection',
                        'password_management' => 'Password management',
                        'network_security' => 'Network security',
                        'cloud_storage' => 'Cloud storage',
                    ],
                    'type_label' => 'Content type',
                    'type_aria' => 'Content type',
                    'type_options' => [
                        'article' => 'Text content',
                        'video' => 'Video content',
                    ],
                    'type_help' => 'Choose the appropriate type to show only the relevant fields.',
                    'article_body_label' => 'Text content',
                    'article_body_placeholder' => 'Write the content here... (long text supported)',
                    'article_body_hint' => 'Will be stored in the `body` column of the content table.',
                    'media_url_label' => 'Media URL (media_url)',
                    'media_url_placeholder' => 'Enter a media link: YouTube, Vimeo, or external file',
                    'media_url_hint' => 'Example: https://www.youtube.com/watch?v=xxxx — stored in `media_url`.',
                    'description_label' => 'Content description',
                    'description_placeholder' => 'Write a brief description for the content...',
                    'difficulty_label' => 'Difficulty level',
                    'difficulty_placeholder' => 'Choose a level',
                    'difficulty_options' => [
                        'beginner' => 'Beginner',
                        'intermediate' => 'Intermediate',
                        'advanced' => 'Advanced',
                    ],
                    'duration_label' => 'Expected reading/viewing time (minutes)',
                    'duration_placeholder' => 'Example: 5',
                    'points_label' => 'Reward points',
                    'status_label' => 'Publication status',
                    'thumbnail_label' => 'Thumbnail URL (thumbnail_url)',
                    'thumbnail_placeholder' => 'https://...',
                    'featured_label' => 'Featured content',
                ],
                'alerts' => [
                    'body_required' => 'Please write the text content.',
                    'media_required' => 'Please provide the appropriate media link for this type.',
                ],
            ],
        ],
    ],
];
