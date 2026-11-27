<?php

namespace App\Console\Commands;

use App\Services\InstagramFeedService;
use Illuminate\Console\Command;

class SyncInstagramMedia extends Command
{
    protected $signature = 'craftyvok:sync-instagram';

    protected $description = 'Sync Craftyvok Instagram videos and story highlights';

    public function handle(InstagramFeedService $instagram): int
    {
        $count = $instagram->sync();
        $this->info("Synced {$count} Instagram videos and highlights.");

        return self::SUCCESS;
    }
}
