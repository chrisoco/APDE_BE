<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum CampaignStatus: string
{
    use HasEnumHelpers;

    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case COMPLETED = 'completed';
}
