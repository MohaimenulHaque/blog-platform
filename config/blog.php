<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reading Time
    |--------------------------------------------------------------------------
    |
    | The average reading speed, in words per minute, used to calculate the
    | reading time of a post automatically from its content.
    |
    */
    'reading_time' => [
        'words_per_minute' => (int) env('BLOG_READING_WORDS_PER_MINUTE', 200),
        'min_minutes' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Images
    |--------------------------------------------------------------------------
    |
    | Validation rules applied to images uploaded for posts and editor content.
    |
    */
    'images' => [
        'max_size_kb' => (int) env('BLOG_IMAGE_MAX_KB', 5120),
        'mimes' => ['jpeg', 'jpg', 'png', 'gif', 'webp'],
        'max_width' => (int) env('BLOG_IMAGE_MAX_WIDTH', 4000),
        'max_height' => (int) env('BLOG_IMAGE_MAX_HEIGHT', 4000),
        'featured_dir' => 'posts/featured',
        'thumbnail_dir' => 'posts/thumbnails',
        'editor_dir' => 'posts/editor',
        'media_dir' => 'media',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Default number of records shown on admin and public index pages.
    |
    */
    'pagination' => [
        'admin_posts' => 15,
        'admin_categories' => 15,
        'admin_tags' => 15,
        'admin_authors' => 15,
        'admin_comments' => 15,
        'admin_users' => 15,
        'admin_media' => 30,
        'admin_subscribers' => 15,
        'public_posts' => 9,
        'public_categories' => 12,
        'public_tags' => 24,
        'public_authors' => 12,
        'notifications' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    |
    | Controls how public comments behave. When auto_approve is enabled new
    | comments are published immediately; otherwise they sit in a pending
    | queue until an admin or editor approves them.
    |
    */
    'comments' => [
        'auto_approve' => (bool) env('BLOG_COMMENTS_AUTO_APPROVE', false),
        'max_length' => (int) env('BLOG_COMMENTS_MAX_LENGTH', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Form
    |--------------------------------------------------------------------------
    |
    | The email address contact form submissions are delivered to.
    |
    */
    'contact_recipient' => env('BLOG_CONTACT_RECIPIENT'),

    /*
    |--------------------------------------------------------------------------
    | Newsletter
    |--------------------------------------------------------------------------
    |
    | Behaviour of the newsletter subscription feature.
    |
    */
    'newsletter' => [
        'from_name' => env('BLOG_NEWSLETTER_FROM_NAME', config('app.name')),
    ],
];
