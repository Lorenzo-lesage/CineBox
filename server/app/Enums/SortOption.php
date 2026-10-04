<?php

namespace App\Enums;

enum SortOption: string
{
    case Popular = 'popular';
    case TopRated = 'top_rated';
    case Newest = 'newest';
    case Oldest = 'oldest';
    case TitleAz = 'title_az';
    case TitleZa = 'title_za';
}
