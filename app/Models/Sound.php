<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sound extends Model
{
    protected $fillable = [
        'freesound_id',
        'name',
        'username',
        'preview_url',
        'waveform_url',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
    ];

    /**
     * The sound boards this sound belongs to.
     */
    public function soundBoards(): BelongsToMany
    {
        return $this->belongsToMany(SoundBoard::class)
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Find or create a sound from Freesound API data.
     */
    public static function findOrCreateFromApi(array $soundData): self
    {
        return static::firstOrCreate(
            ['freesound_id' => (string) $soundData['id']],
            [
                'name'         => $soundData['name'],
                'username'     => $soundData['username'],
                'preview_url'  => $soundData['previews']['preview-lq-mp3'] ?? null,
                'waveform_url' => $soundData['images']['waveform_m'] ?? null,
                'raw_data'     => $soundData,
            ]
        );
    }
}
