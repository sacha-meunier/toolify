<?php

namespace App\Enums;

enum Category: string
{
    public const MAX_CATEGORIES_PER_TOOL = 5;

    case ArtificialIntelligence = 'artificial_intelligence';
    case Business = 'business';
    case Collaboration = 'collaboration';
    case Communication = 'communication';
    case Crm = 'crm';
    case DeveloperTools = 'developer_tools';
    case Education = 'education';
    case Entertainment = 'entertainment';
    case Finance = 'finance';
    case Lifestyle = 'lifestyle';
    case Medical = 'medical';
    case News = 'news';
    case PhotoAndVideo = 'photo_and_video';
    case Productivity = 'productivity';
    case RealEstate = 'real_estate';
    case Reference = 'reference';
    case Shopping = 'shopping';
    case SocialNetworking = 'social_networking';
    case Sports = 'sports';
    case Utilities = 'utilities';

    public function label(): string
    {
        return match ($this) {
            self::ArtificialIntelligence => 'Artificial intelligence',
            self::Business => 'Business',
            self::Collaboration => 'Collaboration',
            self::Communication => 'Communication',
            self::Crm => 'CRM',
            self::DeveloperTools => 'Developer Tools',
            self::Education => 'Education',
            self::Entertainment => 'Entertainment',
            self::Finance => 'Finance',
            self::Lifestyle => 'Lifestyle',
            self::Medical => 'Medical',
            self::News => 'News',
            self::PhotoAndVideo => 'Photo and video',
            self::Productivity => 'Productivity',
            self::RealEstate => 'Real estate',
            self::Reference => 'Reference',
            self::Shopping => 'Shopping',
            self::SocialNetworking => 'Social Networking',
            self::Sports => 'Sports',
            self::Utilities => 'Utilities',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ArtificialIntelligence => 'Artificial Intelligence (AI) apps provide users with AI-powered solutions to their everyday tasks and problems.',
            self::Business => 'Business apps assist with running a business or provide a means to collaborate, edit, or share content.',
            self::Collaboration => 'Collaboration apps provide a platform for multiple users to collaborate, communicate, and interact in real-time.',
            self::Communication => 'Communication apps allow users to communicate with others by text, voice, or video.',
            self::Crm => 'Customer Relationship Management (CRM) apps help businesses track and manage customer relationships through various interactions and data.',
            self::DeveloperTools => 'Developer Tools apps provide tools for app development, management, and distribution.',
            self::Education => 'Education apps provide an interactive learning experience on a specific skill or subject.',
            self::Entertainment => 'Entertainment apps are interactive and designed to entertain and inform the user, and which contain audio, visual, or other content.',
            self::Finance => 'Finance apps perform financial transactions or assist the user with business or personal financial matters.',
            self::Lifestyle => 'Lifestyle apps are related to a general-interest subject matter or service.',
            self::Medical => 'Medical apps are focused on medical education, information management, or health reference for patients or healthcare professionals.',
            self::News => 'News apps provide information about current events or developments in areas of interest such as politics, entertainment, business, science, technology, and so on.',
            self::PhotoAndVideo => 'Photo & Video apps assist in capturing, editing, managing, storing, or sharing photos and videos.',
            self::Productivity => 'Productivity apps make a specific process or task more organized or efficient.',
            self::RealEstate => 'Real Estate apps provide users a platform to search, buy, sell, or rent real estate properties.',
            self::Reference => 'Reference apps assist the user in accessing or retrieving information.',
            self::Shopping => 'Shopping apps support the purchase of consumer goods or materially enhance the shopping experience.',
            self::SocialNetworking => 'Social Networking apps connect people by means of text, voice, photo, or video. Apps that contribute to community development.',
            self::Sports => 'Sports apps are related to professional, amateur, collegiate, or recreational sporting activities.',
            self::Utilities => 'Utilities apps enable the user to solve a problem or complete a specific task.',
        };
    }
}
