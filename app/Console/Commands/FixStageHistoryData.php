<?php

namespace App\Console\Commands;

use App\Models\PrLotPrstage;
use App\Models\PrItemPrstage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixStageHistoryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:stage-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix stage_history field that contains timestamp data instead of stage IDs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting stage_history data cleanup...');

        $lotFixed = 0;
        $itemFixed = 0;

        // Fix PrLotPrstage records
        $this->info('Processing Lot stages...');

        $lotRecords = PrLotPrstage::whereNotNull('stage_history')
            ->orderBy('procID')
            ->orderBy('created_at')
            ->get()
            ->groupBy('procID');

        foreach ($lotRecords as $procID => $stages) {
            $previousStageId = null;

            foreach ($stages as $index => $stage) {
                // Check if stage_history looks like a timestamp (contains date/time characters)
                if ($this->isTimestamp($stage->stage_history)) {
                    // Set to previous stage ID or null if first record
                    $stage->stage_history = $previousStageId ? (string)$previousStageId : null;
                    $stage->save();
                    $lotFixed++;
                }

                $previousStageId = $stage->pr_stage_id;
            }
        }

        // Fix PrItemPrstage records
        $this->info('Processing Item stages...');

        $itemRecords = PrItemPrstage::whereNotNull('stage_history')
            ->orderBy('procID')
            ->orderBy('prItemID')
            ->orderBy('created_at')
            ->get()
            ->groupBy(function($item) {
                return $item->procID . '-' . $item->prItemID;
            });

        foreach ($itemRecords as $key => $stages) {
            $previousStageId = null;

            foreach ($stages as $index => $stage) {
                // Check if stage_history looks like a timestamp
                if ($this->isTimestamp($stage->stage_history)) {
                    // Set to previous stage ID or null if first record
                    $stage->stage_history = $previousStageId ? (string)$previousStageId : null;
                    $stage->save();
                    $itemFixed++;
                }

                $previousStageId = $stage->pr_stage_id;
            }
        }

        $this->info("✓ Fixed {$lotFixed} Lot stage records");
        $this->info("✓ Fixed {$itemFixed} Item stage records");
        $this->info('Stage history cleanup completed!');

        return 0;
    }

    /**
     * Check if a value looks like a timestamp
     *
     * @param mixed $value
     * @return bool
     */
    private function isTimestamp($value): bool
    {
        if (is_null($value)) {
            return false;
        }

        $stringValue = (string)$value;

        // Check if it contains date/time patterns
        // Timestamps typically contain: YYYY-MM-DD or HH:MM:SS or both
        return preg_match('/\d{4}-\d{2}-\d{2}/', $stringValue) ||
               preg_match('/\d{2}:\d{2}:\d{2}/', $stringValue) ||
               strlen($stringValue) > 10; // Stage IDs should be short (1-2 digits)
    }
}
