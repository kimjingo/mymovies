<?php

namespace App\Events;

use App\Models\UserMedia;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaLiked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userMedia;
    public $liker;
    public $likesCount;

    /**
     * Create a new event instance.
     */
    public function __construct(UserMedia $userMedia, User $liker, int $likesCount)
    {
        $this->userMedia = $userMedia;
        $this->liker = $liker;
        $this->likesCount = $likesCount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('media-likes'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'user_media_id' => $this->userMedia->id,
            'liker_name' => $this->liker->name,
            'likes_count' => $this->likesCount,
            'media_title' => $this->userMedia->mediaPool->title,
        ];
    }
}
