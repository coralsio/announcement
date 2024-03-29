<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Corals\Modules\Announcement\Models\Announcement;
use Corals\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AnnouncementsTest extends TestCase
{
    use DatabaseTransactions;

    protected $announcement;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $user = User::query()->whereHas('roles', function ($query) {
            $query->where('name', 'superuser');
        })->first();
        Auth::loginUsingId($user->id);
    }

    public function test_announcements_store()
    {
        $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');

        $response = $this->post(
            'announcements',
            [
                'title' => 'announcement',
                'starts_at' => $startDate,
                'ends_at' => $endDate,
            ]
        );

        $this->announcement = Announcement::query()->where('starts_at', $startDate)
            ->where('ends_at', $endDate)->first();

        $response->assertDontSee('The given data was invalid')
            ->assertRedirect('announcements');

        $this->assertDatabaseHas('announcements', [
            'starts_at' => $this->announcement->starts_at,
            'ends_at' => $this->announcement->ends_at,
        ]);
    }

    public function test_announcements_show()
    {
        $this->test_announcements_store();

        if ($this->announcement) {
            $response = $this->get('announcements/' . $this->announcement->hashed_id);

            $response->assertViewIs('Announcement::announcements.show')->assertStatus(200);
        }
        $this->assertTrue(true);
    }

    public function test_announcements_edit()
    {
        $this->test_announcements_store();

        if ($this->announcement) {
            $response = $this->get('announcements/' . $this->announcement->hashed_id . '/edit');

            $response->assertViewIs('Announcement::announcements.create_edit')->assertStatus(200);
        }
        $this->assertTrue(true);
    }

    public function test_announcements_update()
    {
        $this->test_announcements_store();

        if ($this->announcement) {
            $response = $this->put('announcements/' . $this->announcement->hashed_id, [
                'title' => $this->announcement->title,
                'starts_at' => $this->announcement->starts_at,
                'ends_at' => $this->announcement->ends_at,
            ]);

            $response->assertRedirect('announcements');
            $this->assertDatabaseHas('announcements', [
                'title' => $this->announcement->title,
                'starts_at' => $this->announcement->starts_at,
                'ends_at' => $this->announcement->ends_at,
            ]);
        }

        $this->assertTrue(true);
    }

    public function test_announcements_delete()
    {
        $this->test_announcements_store();

        if ($this->announcement) {
            $response = $this->delete('announcements/' . $this->announcement->hashed_id);

            $response->assertStatus(200)->assertSeeText('Announcement has been deleted successfully.');

            $this->isSoftDeletableModel(Announcement::class);
            $this->assertDatabaseMissing('announcements', [
                'title' => $this->announcement->title,
                'starts_at' => $this->announcement->starts_at,
                'ends_at' => $this->announcement->ends_at,
            ]);
        }
        $this->assertTrue(true);
    }
}
