<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Message extends Model
{
    use HasFactory;

    // Table name is 'Message'
    protected $table = 'Message';
    use SoftDeletes;
    // The status values
    public const STATUS_READ = 'read';
    public const STATUS_UNREAD = 'unread';
    public const STATUS_SOURCE_ARCHIVED = 'source_archived';
    public const STATUS_DESTINATION_ARCHIVED = 'destination_archived';
    public const STATUS_BOTH_ARCHIVED = 'both_archived';

    protected $fillable = [
        's_user_id',
        'd_user_id',
        'subject',
        'message',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the source user (sender).
     */
    public function sourceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 's_user_id');
    }

    /**
     * Get the destination user (receiver).
     */
    public function destinationUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'd_user_id');
    }

    public function scopeFilter($query, Request $request)
    {

        $query->when($request->filled('id'), function ($q) use ($request) {
            $q->where('id', $request->id);
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('sourceUser'), function ($q) use ($request) {
            $q->where('s_user_id', $request->sourceUser);
        });
        $query->when($request->filled('destinationUser'), function ($q) use ($request) {
            $q->where('d_user_id', $request->destinationUser);
        });

        $query->when($request->filled('date_message'), function ($q) use ($request) {
            $q->where('created_at', '>=', $request->date_message);
        });

        return $query;
    }
}
