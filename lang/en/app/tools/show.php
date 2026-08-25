<?php

return [
    'breadcrumb_search' => 'Search',

    'stat_rating' => 'Rating',
    'stat_collections' => 'Collections',
    'stat_followers' => 'Followers',
    'stat_views' => 'Views',

    'about_heading' => 'About',

    'links_heading' => 'Links',
    'not_linked_yet' => 'Not linked yet',

    'details_heading' => 'Details',
    'detail_company' => 'Company',
    'detail_category' => 'Category',
    'detail_pricing' => 'Pricing',
    'detail_added' => 'Added',
    'detail_founded' => 'Founded',
    'detail_first_release' => 'First release',
    'detail_headquarters' => 'Headquarters',
    'detail_headcount' => 'Headcount',
    'detail_status' => 'Status',

    'gallery_heading' => 'Gallery',
    'gallery_title' => 'A closer look at :name',
    'gallery_intro' => 'Browse detailed visuals that showcase the experience, features, and design across every variant.',
    'gallery_shots' => [
        'overview' => ['label' => 'Overview', 'description' => 'A look at the overview experience.'],
        'workflow' => ['label' => 'Workflow', 'description' => 'A look at the workflow experience.'],
        'collaboration' => ['label' => 'Collaboration', 'description' => 'A look at the collaboration experience.'],
    ],

    'pricing_heading' => 'Pricing',
    'pricing_plans_count' => '{1} :count plan|[2,*] :count plans',
    'pricing_plans' => [
        [
            'name' => 'Free',
            'price' => '$0',
            'features' => ['Unlimited workspaces', '2 teams', 'Max 20 tools in stack'],
        ],
        [
            'name' => 'Basic',
            'price' => '$10 per user/month',
            'features' => ['All free features +', 'Item title 1', 'Item title 2', 'Item title 3'],
        ],
        [
            'name' => 'Business',
            'price' => '$16 per user/month',
            'features' => ['All basic features +'],
        ],
        [
            'name' => 'Entreprise',
            'price' => 'Custom',
            'features' => ['All business features +'],
        ],
    ],

    'reviews_heading' => 'What web users have to say',
    'cta_view_all_reviews' => 'View all reviews',
    'cta_write_a_review' => 'Write a review',
    'review_summary_row' => ':percent% · :reviews reviews',
    'reviews' => [
        [
            'name' => 'Lucie',
            'role' => 'Product Designer · Mistral',
            'stars' => 5,
            'ago' => '3d ago',
            'body' => 'The cycle model finally clicked with our team. We replaced three tools with one and shipped two cycles before anyone asked for a feature back.',
        ],
        [
            'name' => 'Raj',
            'role' => 'UX Researcher · Innovate Labs',
            'stars' => 3,
            'ago' => '2d ago',
            'body' => 'User feedback led us to a breakthrough in our onboarding process. Implementing interactive tutorials resulted in a 30% decrease in drop-off rates.',
        ],
        [
            'name' => 'Maya',
            'role' => 'Visual Designer · PixelCraft',
            'stars' => 2,
            'ago' => '1w ago',
            'body' => "Revisiting our brand's color palette has received positive feedback from stakeholders. The fresh look aligns better with our mission and resonates with our audience.",
        ],
    ],

    'activity_heading' => 'Activity',
    'activity_title' => 'Latest releases',
    'cta_view_all_activity' => 'View all activity',
    'releases' => [
        [
            'title' => 'User feedback tool',
            'description' => 'A new feature that allows users to submit feedback directly from the app.',
            'date' => 'June 12',
        ],
        [
            'title' => 'Real-time notifications',
            'description' => 'Users receive instant alerts for important updates and messages.',
            'date' => 'June 12',
        ],
        [
            'title' => 'Contrast slider',
            'description' => 'An option in the settings to choose the right amount of contrast.',
            'date' => 'June 12',
        ],
    ],
];
