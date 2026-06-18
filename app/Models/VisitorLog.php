<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'visitor_hash',
    'ip_hash',
    'user_agent_hash',
    'first_path',
    'last_path',
    'visited_on',
    'first_seen_at',
    'last_seen_at',
    'visit_count',
])]
class VisitorLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'visited_on' => 'date',
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'visit_count' => 'integer',
        ];
    }
}
