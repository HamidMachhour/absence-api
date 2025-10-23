<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Friendship extends Model
{
    use HasFactory;

    // Table name is 'Message'
    protected $table = 'Friendship';
    use SoftDeletes;
    // The status values
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'userId',
        'friend',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

  
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }


    public function friend(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend');
    }

    public function scopeFilter($query, Request $request)
    {

        $query->when($request->filled('id'), function ($q) use ($request) {
            $q->where('id', $request->id);
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('user'), function ($q) use ($request) {
            $q->where('userId', $request->user);
        });
        $query->when($request->filled('friend'), function ($q) use ($request) {
            $q->where('friend', $request->friend);
        });

        $query->when($request->filled('date_friendship'), function ($q) use ($request) {
            $q->where('created_at', '>=', $request->date_friendship);
        });

        return $query;
    }
}
