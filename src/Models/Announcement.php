<?php

namespace Corals\Modules\Announcement\Models;

use Carbon\Carbon;
use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Traits\ModelPropertiesTrait;
use Corals\Foundation\Transformers\PresentableTrait;
use Corals\User\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class Announcement extends BaseModel implements HasMedia
{
    use PresentableTrait;
    use LogsActivity;
    use ModelPropertiesTrait;
    use HasRoles;
    use InteractsWithMedia ;

    public $guard_name = 'web';

    public $mediaCollectionName = 'announcement-media-collection';

    protected $casts = [
        'roles' => 'json',
        'properties' => 'json',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'show_immediately' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'announcement.models.announcement';


    protected $guarded = ['id'];

    /**
     * @return null|string
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversion
     */
    public function getImageAttribute()
    {
        $media = $this->getFirstMedia($this->mediaCollectionName);

        if ($media) {
            return $media->getFullUrl();
        }

        return null;
    }

    public function tracking()
    {
        return $this->hasMany(AnnouncementTracking::class);
    }

    public function markAsRead(User $user = null)
    {
        if (is_null($user)) {
            $user = user();
        }

        $this->tracking()->create([
            'user_id' => $user->id,
            'read_at' => Carbon::now(),
        ]);
    }

    /**
     * @param User|null $user
     * @param bool $boolean
     * @return bool|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|null|object
     */
    public function isRead(User $user = null)
    {
        if (is_null($user)) {
            $user = user();
        }

        $announcementTracking = $this->tracking()->where('user_id', $user->id)->first();

        return $announcementTracking;
    }
}
