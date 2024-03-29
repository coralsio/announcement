<?php

namespace Corals\Modules\Announcement\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Announcement\Models\Announcement;
use Corals\User\Models\User;

class AnnouncementPolicy extends BasePolicy
{
    protected $administrationPermission = 'Administrations::admin.announcement';

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('Announcement::announcement.view')) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Announcement::announcement.create');
    }

    /**
     * @param User $user
     * @param Announcement $announcement
     * @return bool
     */
    public function update(User $user, Announcement $announcement)
    {
        if ($user->can('Announcement::announcement.update')) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param Announcement $announcement
     * @return bool
     */
    public function destroy(User $user, Announcement $announcement)
    {
        if ($user->can('Announcement::announcement.delete')) {
            return true;
        }

        return false;
    }
}
