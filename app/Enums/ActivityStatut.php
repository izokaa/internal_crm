<?php

namespace App\Enums;

enum ActivityStatut: string
{
    case OVERDUE = 'Overdue';
    case TODO = 'To Do';
    case UPCOMING = 'Upcoming';
    case COMPLETED = 'Completed';
}
