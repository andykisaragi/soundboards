<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SoundBoard extends Model
{
    protected $fillable = [
        'title',
        'search_term',
    ];

    /**
     * The sounds on this board (many-to-many).
     */
    public function sounds(): BelongsToMany
    {
        return $this->belongsToMany(Sound::class)
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * Add a sound from Freesound API data to this board.
     * Reuses the Sound record if it already exists in the DB.
     */
    public function addSound(array $soundData, int $sortOrder = 0): void
    {
        $sound = Sound::findOrCreateFromApi($soundData);

        if (!$this->sounds()->where('sound_id', $sound->id)->exists()) {
            $this->sounds()->attach($sound->id, ['sort_order' => $sortOrder]);
        }
    }

    /**
     * Add multiple sounds at once.
     */
    public function addSounds(array $soundsData): void
    {
        foreach ($soundsData as $index => $soundData) {
            $this->addSound($soundData, $index);
        }
    }

    /**
     * Remove a sound from this board by freesound ID.
     */
    public function removeSound(string $freesoundId): void
    {
        $sound = Sound::where('freesound_id', $freesoundId)->first();
        if ($sound) {
            $this->sounds()->detach($sound->id);
        }
    }

    /**
     * Check if a sound (by freesound ID) is already on this board.
     */
    public function hasSound(string $freesoundId): bool
    {
        return $this->sounds()
            ->where('freesound_id', $freesoundId)
            ->exists();
    }
}
