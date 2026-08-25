<?php

namespace App\Enums;

enum Category: string
{
    public const int MAX_CATEGORIES_PER_TOOL = 5;

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
        return __('enums/category.'.$this->value.'.label');
    }

    public function description(): string
    {
        return __('enums/category.'.$this->value.'.description');
    }
}
