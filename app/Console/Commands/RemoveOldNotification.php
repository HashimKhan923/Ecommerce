<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;


class RemoveOldNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-old-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleted = Notification::where('created_at', '<', Carbon::now()->subMonth())->delete();

        $this->info("Deleted {$deleted} old notifications.");
    }
}
