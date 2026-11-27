<?php

namespace App\Services;

use App\Models\SocialPost;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class InstagramFeedService
{
    public function sync(): int
    {
        $userId = config('services.instagram.user_id');
        $token = config('services.instagram.access_token');

        if (! $userId || ! $token) {
            throw new RuntimeException('Set INSTAGRAM_USER_ID and INSTAGRAM_ACCESS_TOKEN before syncing.');
        }

        $baseUrl = 'https://graph.facebook.com/'.config('services.instagram.graph_version');
        $client = Http::acceptJson()->withToken($token)->timeout(20);
        $count = $this->syncVideos($client, $baseUrl, $userId);
        $count += $this->syncHighlights($client, $baseUrl, $userId);

        return $count;
    }

    private function syncVideos(PendingRequest $client, string $baseUrl, string $userId): int
    {
        $response = $client->get("{$baseUrl}/{$userId}/media", [
            'fields' => 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp',
            'limit' => 50,
        ])->throw()->json('data', []);

        $count = 0;
        foreach ($response as $media) {
            if (! in_array($media['media_type'] ?? '', ['VIDEO', 'REELS'], true)) {
                continue;
            }

            SocialPost::updateOrCreate(
                ['instagram_id' => (string) $media['id']],
                [
                    'media_type' => $media['media_type'],
                    'caption' => $media['caption'] ?? null,
                    'media_url' => $media['media_url'] ?? null,
                    'thumbnail_url' => $media['thumbnail_url'] ?? $media['media_url'] ?? null,
                    'permalink' => $media['permalink'] ?? config('services.instagram.profile_url'),
                    'published_at' => $media['timestamp'] ?? null,
                    'is_highlight' => false,
                ],
            );
            $count++;
        }

        return $count;
    }

    private function syncHighlights(PendingRequest $client, string $baseUrl, string $userId): int
    {
        $response = $client->get("{$baseUrl}/{$userId}/highlights", [
            'fields' => 'id,name,media_count,cover_media',
            'limit' => 50,
        ])->throw()->json('data', []);

        foreach ($response as $highlight) {
            $cover = $highlight['cover_media']['url'] ?? null;
            SocialPost::updateOrCreate(
                ['instagram_id' => 'highlight:'.(string) $highlight['id']],
                [
                    'media_type' => 'HIGHLIGHT',
                    'title' => $highlight['name'] ?? 'Instagram highlight',
                    'caption' => null,
                    'media_url' => null,
                    'thumbnail_url' => $cover,
                    'permalink' => config('services.instagram.profile_url'),
                    'published_at' => null,
                    'is_highlight' => true,
                ],
            );
        }

        return count($response);
    }
}
