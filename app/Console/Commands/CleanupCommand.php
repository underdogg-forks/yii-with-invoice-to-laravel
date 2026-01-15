<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cleanup:old-data 
                            {--days= : Number of days to retain data}
                            {--dry-run : Run without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Cleanup old data from the system';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting cleanup process...');

        $dryRun = $this->option('dry-run');

        // Cleanup old activity logs
        $this->cleanupActivityLogs($dryRun);

        // Cleanup temp files
        $this->cleanupTempFiles($dryRun);

        // Cleanup soft-deleted records
        $this->cleanupSoftDeleted($dryRun);

        // Cleanup old notifications
        $this->cleanupNotifications($dryRun);

        $this->info('Cleanup process completed!');

        return self::SUCCESS;
    }

    /**
     * Cleanup old activity logs.
     */
    protected function cleanupActivityLogs(bool $dryRun): void
    {
        $days = $this->option('days') ?? Config::get('activity.retention_days', 90);
        $cutoffDate = Carbon::now()->subDays($days);

        $count = ActivityLog::where('created_at', '<', $cutoffDate)->count();

        if ($count > 0) {
            $this->info("Found {$count} activity logs older than {$days} days");

            if (!$dryRun) {
                ActivityLog::where('created_at', '<', $cutoffDate)->delete();
                $this->info("Deleted {$count} activity logs");
            }
        } else {
            $this->info('No old activity logs to cleanup');
        }
    }

    /**
     * Cleanup temporary files.
     */
    protected function cleanupTempFiles(bool $dryRun): void
    {
        $tempPath = storage_path('app/temp');

        if (!File::exists($tempPath)) {
            $this->info('No temp directory found');
            return;
        }

        $files = File::files($tempPath);
        $count = 0;

        foreach ($files as $file) {
            // Delete files older than 24 hours
            if (filemtime($file) < time() - 86400) {
                $count++;
                
                if (!$dryRun) {
                    File::delete($file);
                }
            }
        }

        if ($count > 0) {
            $this->info("Found {$count} old temp files");
            
            if (!$dryRun) {
                $this->info("Deleted {$count} temp files");
            }
        } else {
            $this->info('No old temp files to cleanup');
        }
    }

    /**
     * Cleanup soft-deleted records permanently.
     */
    protected function cleanupSoftDeleted(bool $dryRun): void
    {
        $days = 30; // Keep soft-deleted records for 30 days
        $cutoffDate = Carbon::now()->subDays($days);

        // This would need to be implemented for each model with soft deletes
        $this->info('Soft-deleted cleanup not yet implemented for all models');
    }

    /**
     * Cleanup old notifications.
     */
    protected function cleanupNotifications(bool $dryRun): void
    {
        // This would cleanup old read notifications
        $this->info('Notification cleanup not yet implemented');
    }
}
