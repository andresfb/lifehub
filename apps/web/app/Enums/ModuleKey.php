<?php

declare(strict_types=1);

namespace App\Enums;

enum ModuleKey: string
{
    // Public
    case FILE_STORAGE = 'file_storage';
    case CRM = 'crm';
    case BOOKMARKS = 'bookmarks';
    case HABITS = 'habits';
    case NOTES = 'notes';
    case JOURNAL = 'journal';
    case AI_CHATS = 'ai_chats';
    case EMBY = 'emby';
    case TIP_ROUNDER = 'tip_rounder';
    case MEDICINE_TRACKER = 'medicine_tracker';

    // Private
    // TODO: add the private module keys;
}
