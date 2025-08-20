<?php

namespace App\Models;

use App\Enums\RecordStatusConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'text',
        'record_status',
        'user_id',
        'video_id'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function scopeRecord($query, $filter)
    {
        $query->where('record_status', RecordStatusConstant::active);

        return $query;
    }

    public function scopeFilter($query, $filters)
    {
        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
