<?php

namespace App\Models;

use App\Enums\RecordStatusConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Video extends Model
{
    /** @use HasFactory<\Database\Factories\VideoFactory> */
    use HasFactory;

    // protected $guarded = ['id'];

    protected $fillable = [
        'title', 
        'description', 
        'record_status',
        'image',
        'video',
        'duration'
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

    public function scopeRecord($query, $filters)
    {
        $query->where('record_status', RecordStatusConstant::active);

        return $query;
    }

    public function scopeFilter($query, $filters)
    {
        $query->when(
            $filters->filled('search'),
            fn($q) => $q->where('title', 'like', '%' . $filters->search . '%')
        );

        $sort_keys = ['created_at', 'title', 'comments_count'];
        $sort_direction = in_array(strtolower($filters->input('sort_direction')), ['asc', 'desc'])
                            ? strtolower($filters->sort_direction)
                            : 'desc';

        $query->when(
            $filters->filled('sort_by') && in_array($filters->sort_by, $sort_keys), 
            fn($q) => $q->orderBy($filters->sort_by, $sort_direction),
            fn($q) => $q->orderBy('created_at', 'desc')
        );
    
        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // for getting the latest comment
    public function comment()
    {
        return $this->hasOne(Comment::class);
    }

    // for getting the number of comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // for getting the last position watched by viewer
    public function history()
    {
        return $this->hasOne(History::class);
    }

    // for getting the number of views
    public function histories()
    {
        return $this->hasMany(History::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
