<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\BidSchedule;
use App\Models\ModeOfProcurement;
use App\Models\PrSvp;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use App\Models\Procurement;
use App\Models\MopLot;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModeOfProcurementBulkEditPerLotPage extends Component
{
    public array $items = [];
    public Collection $modeOfProcurements;
    public array $procurementIds = [];
    public bool $showHistory = false;
    public ?string $historyForKey = null;
    public array $bulkEdit = [];
    public array $scheduleValidationErrors = [];
    public int $activeTab = 1;
    public bool $showAddForm = false;
    public array $queryParams = [];

    // Selection functionality
    public array $selectedItems = [];
    public bool $selectAll = false;
    public bool $showBulkEditModal = false;

    // Post-Procurement Selection & Bulk Edit
    public array $selectedPostItems = [];
    public bool $selectAllPost = false;
    public bool $showPostBulkEditModal = false;
    public array $postBulkEditData = [];

    // Post-Procurement Tab Fields
    public ?string $resolutionAwardNumber = null;
    public ?string $noticeOfAwardNumber = null;
    public ?string $noticeOfAward = null;
    public ?string $resolutionAwardDate = null;
    public ?float $awardedAmount = null;
    public ?string $philgepsNoticeOfAwardNo = null;
    public ?string $philgepsPostingOfAward = null;
    public ?int $supplier_id = null;
    public Collection $suppliers;

    public function mount(): void
    {
        $this->queryParams = request()->query();
        $this->procurementIds = request()->query('items', []);

        if (empty($this->procurementIds)) {
            session()->flash('alert', [
                'type' => 'warning',
                'title' => 'No Items Selected',
                'message' => 'Please select procurement items to edit.'
            ]);
            $this->redirect(route('mode-of-procurement.index'));
            return;
        }

        $this->modeOfProcurements = ModeOfProcurement::orderBy('id', 'asc')->get();
        $this->suppliers = \App\Models\Supplier::all();
        $this->loadProcurementData();
        $this->populateBulkEditData();
        $this->loadPostProcurementData();
    }

    // Selection methods
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = collect($this->getCurrentItems())
                ->pluck('procID')
                ->toArray();
        } else {
            $this->selectedItems = [];
        }
        $this->dispatch('$refresh');
    }

    public function updatedSelectedItems()
    {
        $currentProcIds = collect($this->getCurrentItems())->pluck('procID')->toArray();
        $selectedUnique = array_unique($this->selectedItems);
        $this->selectAll = !empty($currentProcIds) &&
            count($selectedUnique) === count($currentProcIds) &&
            empty(array_diff($selectedUnique, $currentProcIds));
        $this->dispatch('$refresh');
    }

    public function clearSelections()
    {
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function openBulkEditModal()
    {
        if (empty($this->selectedItems)) {
            LivewireAlert::title('No Items Selected')
                ->warning()
                ->text('Please select at least one procurement to edit.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Validate selected items before opening modal
        $validation = $this->validateBulkEditSelection();

        if (!$validation['valid']) {
            $errorMessage = implode(' ', $validation['errors']);
            LivewireAlert::title('Bulk Edit Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $this->populateBulkEditData();
        $this->showBulkEditModal = true;
    }

    public function closeBulkEditModal()
    {
        $this->showBulkEditModal = false;
        $this->clearBulkEditScheduleFields();
    }

    /**
     * Validate bulk edit selection before opening modal
     * Ensures all selected items have same mode and identical field values
     */
    private function validateBulkEditSelection(): array
    {
        $errors = [];
        $modes = [];
        $scheduleData = [];
        $prNumbers = [];
        $amounts = [];

        // Get current items that match selected IDs
        $selectedItemsData = collect($this->items)
            ->filter(function ($item) {
                return in_array($item['procID'], $this->selectedItems);
            })
            ->unique('procID')
            ->values();

        if ($selectedItemsData->isEmpty()) {
            return [
                'valid' => false,
                'errors' => ['No valid items found in selection.']
            ];
        }

        foreach ($selectedItemsData as $item) {
            $procId = $item['procID'];
            $prNumbers[] = $item['pr_number'];
            $modeId = $item['mode_of_procurement_id'] ?? null;

            // Track ABC amounts
            $amounts[$item['pr_number']] = $item['abc'] ?? 0;

            // Track modes
            if ($modeId) {
                if (!isset($modes[$modeId])) {
                    $modes[$modeId] = [];
                }
                $modes[$modeId][] = $item['pr_number'];
            }

            // Extract schedule data for comparison
            $scheduleData[$procId] = [
                'mode_of_procurement_id' => $modeId,
                'bidding_number' => $item['bidding_number'] ?? null,
                'ib_number' => $item['ib_number'] ?? null,
                'philgeps_posting_ref_no' => $item['philgeps_posting_ref_no'] ?? null,
                'ads_post_ib' => $item['ads_post_ib'] ?? null,
                'pre_proc_conference' => $item['pre_proc_conference'] ?? null,
                'list_invited_observers' => $item['list_invited_observers'] ?? null,
                'obsrvr_prebid_conf' => $item['obsrvr_prebid_conf'] ?? null,
                'obsrvr_eligibility' => $item['obsrvr_eligibility'] ?? null,
                'obsrvr_sub_open_of_bid' => $item['obsrvr_sub_open_of_bid'] ?? null,
                'obsrvr_bid' => $item['obsrvr_bid'] ?? null,
                'obsrvr_post_qual' => $item['obsrvr_post_qual'] ?? null,
                'pre_bid_conf' => $item['pre_bid_conf'] ?? null,
                'eligibility_check' => $item['eligibility_check'] ?? null,
                'sub_open_bids' => $item['sub_open_bids'] ?? null,
                'bid_evaluation_date' => $item['bid_evaluation_date'] ?? null,
                'post_qualification_date' => $item['post_qualification_date'] ?? null,
                'bidding_result' => $item['bidding_result'] ?? null,
                'resolution_number_mop' => $item['resolution_number_mop'] ?? null,
                'rfq_no' => $item['rfq_no'] ?? null,
                'canvass_date' => $item['canvass_date'] ?? null,
                'date_returned_of_canvass' => $item['date_returned_of_canvass'] ?? null,
                'abstract_of_canvass_date' => $item['abstract_of_canvass_date'] ?? null,
            ];
        }

        // Check if all items have the same mode
        if (count($modes) > 1) {
            $modeDetails = [];
            foreach ($modes as $modeId => $prs) {
                $modeName = $this->modeOfProcurements->firstWhere('id', $modeId)?->modeofprocurements ?? 'Unknown';
                $modeDetails[] = "{$modeName}: " . implode(', ', $prs);
            }
            $errors[] = "Items have different modes: " . implode('; ', $modeDetails) . ". Bulk edit requires all selected PRs to have the same mode of procurement.";
        }

        if (empty($modes)) {
            $errors[] = "No valid mode selected for PRs: " . implode(', ', $prNumbers);
        }

        // Check if all items have identical schedule data (only when all have same mode)
        if (!empty($modes) && count($modes) === 1) {
            $scheduleHashes = [];
            $prNumbersByHash = [];

            foreach ($scheduleData as $procId => $schedule) {
                $hash = md5(json_encode($schedule));
                $scheduleHashes[$procId] = $hash;

                $prNumber = $selectedItemsData->firstWhere('procID', $procId)['pr_number'];
                if (!isset($prNumbersByHash[$hash])) {
                    $prNumbersByHash[$hash] = [];
                }
                $prNumbersByHash[$hash][] = $prNumber;
            }

            $uniqueHashes = array_unique($scheduleHashes);

            if (count($uniqueHashes) > 1) {
                // Find the minority group (items with different data)
                $hashCounts = array_count_values($scheduleHashes);
                arsort($hashCounts);
                $majorityHash = array_key_first($hashCounts);

                $differentPRs = [];
                foreach ($scheduleHashes as $procId => $hash) {
                    if ($hash !== $majorityHash) {
                        $prNumber = $selectedItemsData->firstWhere('procID', $procId)['pr_number'];
                        $differentPRs[] = $prNumber;
                    }
                }

                $prList = implode(', ', $differentPRs);
                $errors[] = "Field mismatch: PR" . (count($differentPRs) > 1 ? 's' : '') . " {$prList} " .
                    (count($differentPRs) > 1 ? 'have' : 'has') . " different field values from the others. Bulk edit requires all selected PRs to have identical field values.";
            }
        }

        // Check amount threshold consistency
        $below200k = [];
        $above200k = [];

        foreach ($amounts as $prNum => $amount) {
            if ($amount < 200000) {
                $below200k[] = $prNum;
            } else {
                $above200k[] = $prNum;
            }
        }

        $amountThreshold = null;
        if (!empty($below200k) && !empty($above200k)) {
            $errors[] = "Mixed amount thresholds: Below ₱200,000: " . implode(', ', $below200k) .
                "; ₱200,000 and above: " . implode(', ', $above200k) . ". Bulk edit requires all PRs to have the same amount threshold.";
        } elseif (!empty($below200k)) {
            $amountThreshold = 'Below ₱200,000.00';
        } else {
            $amountThreshold = '₱200,000.00 and Above';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'commonMode' => count($modes) === 1 ? array_key_first($modes) : null,
            'prNumbers' => $prNumbers,
            'amountThreshold' => $amountThreshold
        ];
    }

    /**
     * Standardized method to check if a value is considered "filled"
     * Handles: null, empty string, whitespace-only strings
     * Returns true if value has meaningful content
     */
    private function hasValue($value): bool
    {
        // null check first
        if (is_null($value)) {
            return false;
        }

        // Convert to string and trim
        $stringValue = trim((string) $value);

        // Check if empty after trimming
        return $stringValue !== '';
    }

    private function hasAnyValue(array $fields): bool
    {
        foreach ($fields as $field) {
            // Handle both string keys (for bulkEdit array) and direct values (for post fields)
            $value = is_string($field) ? ($this->bulkEdit[$field] ?? '') : $field;
            if ($this->hasValue($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Load existing post-procurement data
     * If all PRs have identical post data, pre-fill the form
     * Otherwise, leave fields empty for manual entry
     */
    private function loadPostProcurementData(): void
    {
        $currentItems = $this->getCurrentItems();

        if (empty($currentItems)) {
            return;
        }

        // Get post data from first PR
        $firstRefId = $currentItems[0]['procID'];

        $firstPost = \App\Models\PostProcurement::where('ref_id', $firstRefId)->first();

        if (!$firstPost) {
            return; // No post data exists
        }

        // Check if ALL PRs have identical post data
        $allIdentical = true;

        foreach ($currentItems as $item) {
            $refId = $item['procID'];

            $itemPost = \App\Models\PostProcurement::where('ref_id', $refId)->first();

            // If any PR is missing post data or has different data, don't pre-fill
            if (
                !$itemPost ||
                $itemPost->resolution_award_number !== $firstPost->resolution_award_number ||
                $itemPost->resolution_award_date !== $firstPost->resolution_award_date ||
                $itemPost->notice_of_award_number !== $firstPost->notice_of_award_number ||
                $itemPost->notice_of_award !== $firstPost->notice_of_award ||
                $itemPost->awarded_amount !== $firstPost->awarded_amount ||
                $itemPost->philgeps_notice_of_award_no !== $firstPost->philgeps_notice_of_award_no ||
                $itemPost->philgeps_posting_of_award !== $firstPost->philgeps_posting_of_award ||
                $itemPost->supplier_id !== $firstPost->supplier_id
            ) {
                $allIdentical = false;
                break;
            }
        }

        // Only pre-fill if all PRs have identical post data
        if ($allIdentical) {
            $this->resolutionAwardNumber = $firstPost->resolution_award_number;
            $this->resolutionAwardDate = $firstPost->resolution_award_date;
            $this->noticeOfAwardNumber = $firstPost->notice_of_award_number;
            $this->noticeOfAward = $firstPost->notice_of_award;
            $this->awardedAmount = $firstPost->awarded_amount;
            $this->philgepsNoticeOfAwardNo = $firstPost->philgeps_notice_of_award_no;
            $this->philgepsPostingOfAward = $firstPost->philgeps_posting_of_award;
            $this->supplier_id = $firstPost->supplier_id;
        }
    }

    /**
     * Check if item has any schedule data
     */
    public function itemHasSchedule(array $item): bool
    {
        $scheduleFields = [
            'bidding_number',
            'ib_number',
            'philgeps_posting_ref_no',
            'ads_post_ib',
            'pre_proc_conference',
            'pre_bid_conf',
            'eligibility_check',
            'sub_open_bids',
            'bid_evaluation_date',
            'post_qualification_date',
            'bidding_result',
            'resolution_number_mop',
            'rfq_no',
            'canvass_date',
            'date_returned_of_canvass',
            'abstract_of_canvass_date'
        ];

        foreach ($scheduleFields as $field) {
            if ($this->hasValue($item[$field] ?? '')) {
                return true;
            }
        }

        return false;
    }

    private function validateBulkSchedules(): bool
    {
        $this->scheduleValidationErrors = [];
        $modeId = $this->bulkEdit['mode_of_procurement_id'] ?? null;

        if (!$modeId) {
            $this->scheduleValidationErrors[] = 'Mode of Procurement is required.';
            return false;
        }

        // COMPETITIVE BIDDING MODES (2, 3, 4, 5, 6)
        if (in_array($modeId, [2, 3, 4, 5, 6])) {
            // Validate Bidding Result dependencies
            $biddingResult = $this->bulkEdit['bidding_result'] ?? null;

            if ($this->hasValue($biddingResult)) {
                $missingFields = [];
                $hasPreProcConference = $this->hasValue($this->bulkEdit['pre_proc_conference'] ?? '');

                if (!$hasPreProcConference) {
                    if (!$this->hasValue($this->bulkEdit['bidding_number'] ?? '')) {
                        $missingFields[] = 'Bidding #';
                    }
                    if (!$this->hasValue($this->bulkEdit['ib_number'] ?? '')) {
                        $missingFields[] = 'IB No.';
                    }
                    if (!$this->hasValue($this->bulkEdit['sub_open_bids'] ?? '')) {
                        $missingFields[] = 'Submission & Opening of Bids';
                    }

                    if (!empty($missingFields)) {
                        $fieldsList = implode(', ', $missingFields);
                        $this->scheduleValidationErrors[] = "Competitive Bidding: Cannot set Bidding Result without {$fieldsList} or Pre-Proc Conference.";
                    }
                }

                if ($biddingResult === 'SUCCESSFUL') {
                    $successMissingFields = [];

                    if (!$this->hasValue($this->bulkEdit['bid_evaluation_date'] ?? '')) {
                        $successMissingFields[] = 'Bid Evaluation Date';
                    }
                    if (!$this->hasValue($this->bulkEdit['post_qualification_date'] ?? '')) {
                        $successMissingFields[] = 'Post Qualification Date';
                    }

                    if (!empty($successMissingFields)) {
                        $fieldsList = implode(', ', $successMissingFields);
                        $this->scheduleValidationErrors[] = "Competitive Bidding: {$fieldsList} required for SUCCESSFUL bidding result.";
                    }
                }

                // Validate Resolution Number for Bidding Result
                if (!$this->hasValue($this->bulkEdit['resolution_number_mop'] ?? '')) {
                    $this->scheduleValidationErrors[] = 'Competitive Bidding: Resolution Number is required when Bidding Result is set.';
                }
            }
        }

        // SVP/ALTERNATIVE MODES (7-24)
        if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
            // Validate PhilGEPS requirements based on individual PR ABC
            $currentItems = collect($this->getCurrentItems())
                ->whereIn('procID', $this->selectedItems)
                ->values()
                ->toArray();
            $requiresPhilgeps = false;
            $prNumbersRequiringPhilgeps = [];

            foreach ($currentItems as $item) {
                $procurement = Procurement::find($item['procID']);
                $abc = $procurement ? $procurement->abc : 0;

                if ($abc >= 200000) {
                    $requiresPhilgeps = true;
                    $prNumbersRequiringPhilgeps[] = $procurement->pr_number;
                }
            }

            // If any PR requires PhilGEPS (ABC >= 200K), validate PhilGEPS fields
            if ($requiresPhilgeps) {
                $missingPhilgepsFields = [];

                if (!$this->hasValue($this->bulkEdit['philgeps_posting_ref_no'] ?? '')) {
                    $missingPhilgepsFields[] = 'PhilGEPS Posting Ref No';
                }
                if (!$this->hasValue($this->bulkEdit['ads_post_ib'] ?? '')) {
                    $missingPhilgepsFields[] = 'Advertisement/Posting of IB/REI';
                }

                if (!empty($missingPhilgepsFields)) {
                    $fieldsList = implode(', ', $missingPhilgepsFields);
                    $prList = implode(', ', array_unique($prNumbersRequiringPhilgeps));
                    $this->scheduleValidationErrors[] = "SVP Mode: {$fieldsList} required for PR(s) {$prList} (ABC ≥ ₱200,000).";
                }
            }
        }

        return empty($this->scheduleValidationErrors);
    }

    private function validateExistingScheduleDeletion(): bool
    {
        // Check if user is trying to clear existing schedules without providing new data
        $currentItems = collect($this->getCurrentItems())
            ->whereIn('procID', $this->selectedItems)
            ->values()
            ->toArray();

        foreach ($currentItems as $item) {
            $modeId = $item['mode_of_procurement_id'];

            // For competitive bidding
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $existingFields = [
                    'bidding_number',
                    'ib_number',
                    'philgeps_posting_ref_no',
                    'ads_post_ib',
                    'pre_proc_conference',
                    'list_invited_observers',
                    'obsrvr_prebid_conf',
                    'obsrvr_eligibility',
                    'obsrvr_sub_open_of_bid',
                    'obsrvr_bid',
                    'obsrvr_post_qual',
                    'pre_bid_conf',
                    'eligibility_check',
                    'sub_open_bids',
                    'bid_evaluation_date',
                    'post_qualification_date',
                    'bidding_result',
                    'resolution_number_mop'
                ];

                $hasExistingData = false;
                foreach ($existingFields as $field) {
                    if ($this->hasValue($item[$field] ?? '')) {
                        $hasExistingData = true;
                        break;
                    }
                }

                if ($hasExistingData && !$this->hasAnyValue($existingFields)) {
                    $this->scheduleValidationErrors[] = "Cannot clear all existing bidding schedule data for PR #{$item['pr_number']}.";
                    return false;
                }
            }

            // For SVP/Alternative modes
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                $existingFields = [
                    'resolution_number_mop',
                    'rfq_no',
                    'canvass_date',
                    'date_returned_of_canvass',
                    'abstract_of_canvass_date'
                ];

                $hasExistingData = false;
                foreach ($existingFields as $field) {
                    if ($this->hasValue($item[$field] ?? '')) {
                        $hasExistingData = true;
                        break;
                    }
                }

                if ($hasExistingData && !$this->hasAnyValue($existingFields)) {
                    $this->scheduleValidationErrors[] = "Cannot clear all existing SVP schedule data for PR #{$item['pr_number']}.";
                    return false;
                }
            }
        }

        return true;
    }

    private function canModifySuccessfulBids(): bool
    {
        // Check if user has permission to modify items with post-procurement data
        if (!auth()->user()->can('edit_mode::of::procurement')) {
            $currentItems = collect($this->getCurrentItems())
                ->whereIn('procID', $this->selectedItems)
                ->values()
                ->toArray();

            foreach ($currentItems as $item) {
                $biddingResult = $item['bidding_result'] ?? '';

                if ($biddingResult === 'SUCCESSFUL') {
                    $refId = $item['procID'];

                    $hasPostData = \App\Models\PostProcurement::where('ref_id', $refId)->exists();

                    if ($hasPostData) {
                        $this->scheduleValidationErrors[] = "Cannot modify PR #{$item['pr_number']} - it has a SUCCESSFUL bidding result with post-procurement data. This requires 'Edit Mode of Procurement' permission.";
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function getCurrentItems(): array
    {
        // Group items by procurement and get the one with highest mode_order (current mode)
        $groupedByProc = [];

        foreach ($this->items as $item) {
            $key = 'lot_' . $item['procID'];

            if (!isset($groupedByProc[$key]) || $item['mode_order'] > $groupedByProc[$key]['mode_order']) {
                $groupedByProc[$key] = $item;
            }
        }

        return array_values($groupedByProc);
    }

    private function populateBulkEditData(): void
    {
        // Clear existing data first to prevent leftover data from previous transactions
        $this->bulkEdit = [];

        // Get current items (highest mode_order) from selected PRs only
        if (empty($this->items) || empty($this->selectedItems)) {
            return;
        }

        $currentItems = collect($this->getCurrentItems())
            ->whereIn('procID', $this->selectedItems)
            ->values();

        if (empty($currentItems)) {
            \Log::warning('No current items found for selected procurements', [
                'total_items' => count($this->items),
                'selected_items' => $this->selectedItems
            ]);
            return;
        }

        \Log::info('Found current items for selected', ['count' => count($currentItems)]);

        // Fields to check for identical values
        $fields = [
            'mode_of_procurement_id',
            'bidding_number',
            'ib_number',
            'philgeps_posting_ref_no',
            'ads_post_ib',
            'pre_proc_conference',
            'list_invited_observers',
            'obsrvr_prebid_conf',
            'obsrvr_eligibility',
            'obsrvr_sub_open_of_bid',
            'obsrvr_bid',
            'obsrvr_post_qual',
            'pre_bid_conf',
            'eligibility_check',
            'sub_open_bids',
            'bid_evaluation_date',
            'post_qualification_date',
            'bidding_result',
            'resolution_number_mop',
            'resolution_number',
            'rfq_no',
            'canvass_date',
            'date_returned_of_canvass',
            'abstract_of_canvass_date'
        ];

        // For each field, check if all items have the same value
        foreach ($fields as $field) {
            $values = $currentItems->map(fn($item) => $item[$field] ?? '')->toArray();
            $uniqueValues = array_unique($values);

            // If all values are identical, use that value; otherwise leave empty
            if (count($uniqueValues) === 1) {
                $this->bulkEdit[$field] = reset($values);
            } else {
                $this->bulkEdit[$field] = '';
            }
        }

        \Log::info('Bulk edit populated', [
            'mode_id' => $this->bulkEdit['mode_of_procurement_id'],
            'bidding_number' => $this->bulkEdit['bidding_number']
        ]);
    }

    private function loadProcurementData(): void
    {
        $procurements = Procurement::with(['pr_items', 'mopLots'])
            ->whereIn('procID', $this->procurementIds)
            ->where('procurement_type', 'perLot')  // Only load perLot procurements
            ->orderBy('pr_number')
            ->get();

        // Get all procIDs for schedules
        $procIds = $procurements->pluck('procID')->toArray();

        // Fetch all schedules at once
        $bidSchedules = BidSchedule::whereIn('ref_id', $procIds)->get();
        $prSvps = PrSvp::whereIn('ref_id', $procIds)->get();

        // Build schedule maps
        $scheduleMap = $this->buildScheduleMap($bidSchedules, $prSvps);

        $this->items = [];

        foreach ($procurements as $procurement) {
            // Load all modes for perLot, not just the latest
            $mopLots = $procurement->mopLots()
                ->with('modeOfProcurement')
                ->orderBy('mode_order', 'desc')
                ->get();

            foreach ($mopLots as $mopLot) {
                $this->items[] = $this->buildPerLotRowFromMop($procurement, $mopLot, $scheduleMap);
            }
        }
    }

    private function buildScheduleMap(Collection $bidSchedules, Collection $prSvps): Collection
    {
        $map = collect();

        foreach ($bidSchedules as $schedule) {
            $refId = $schedule->ref_id;
            if (!$map->has($refId)) {
                $map[$refId] = collect();
            }
            $map[$refId][$schedule->mop_uid] = [
                'bidding_number' => $schedule->bidding_number,
                'ib_number' => $schedule->ib_number,
                'philgeps_posting_ref_no' => $schedule->philgeps_posting_ref_no,
                'ads_post_ib' => $schedule->ads_post_ib,
                'pre_proc_conference' => $schedule->pre_proc_conference,
                'list_invited_observers' => $schedule->list_invited_observers,
                'obsrvr_prebid_conf' => $schedule->obsrvr_prebid_conf,
                'obsrvr_eligibility' => $schedule->obsrvr_eligibility,
                'obsrvr_sub_open_of_bid' => $schedule->obsrvr_sub_open_of_bid,
                'obsrvr_bid' => $schedule->obsrvr_bid,
                'obsrvr_post_qual' => $schedule->obsrvr_post_qual,
                'pre_bid_conf' => $schedule->pre_bid_conf,
                'eligibility_check' => $schedule->eligibility_check,
                'sub_open_bids' => $schedule->sub_open_bids,
                'bid_evaluation_date' => $schedule->bid_evaluation_date,
                'post_qualification_date' => $schedule->post_qualification_date,
                'bidding_result' => $schedule->bidding_result,
                'resolution_number_mop' => $schedule->resolution_number_mop,
            ];
        }

        foreach ($prSvps as $schedule) {
            $refId = $schedule->ref_id;
            if (!$map->has($refId)) {
                $map[$refId] = collect();
            }
            $mopUid = $schedule->mop_uid;
            $existing = $map[$refId]->get($mopUid, []);

            $map[$refId][$mopUid] = array_merge($existing, [
                'resolution_number_mop' => $schedule->resolution_number_mop,
                'philgeps_posting_ref_no' => $schedule->philgeps_posting_ref_no,
                'ads_post_ib' => $schedule->ads_post_ib,
                'rfq_no' => $schedule->rfq_no,
                'canvass_date' => $schedule->canvass_date,
                'date_returned_of_canvass' => $schedule->date_returned_of_canvass,
                'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date,
            ]);
        }

        return $map;
    }

    private function buildPerLotRowFromMop(Procurement $procurement, $mopLot, Collection $scheduleMap): array
    {
        $schedule = [];
        if ($mopLot && $scheduleMap->has($procurement->procID)) {
            $schedule = $scheduleMap[$procurement->procID][$mopLot->uid] ?? [];
        }

        return [
            'procID' => $procurement->procID,
            'prItemID' => null,
            'pr_number' => $procurement->pr_number,
            'procurement_program_project' => $procurement->procurement_program_project,
            'abc' => $procurement->abc,
            'procurement_type' => 'perLot',
            'mop_id' => $mopLot?->id,
            'mop_uid' => $mopLot?->uid,
            'mode_of_procurement_id' => $mopLot?->mode_of_procurement_id,
            'mode_order' => $mopLot?->mode_order ?? 0,

            // Schedule fields
            'bidding_number' => $schedule['bidding_number'] ?? '',
            'ib_number' => $schedule['ib_number'] ?? '',
            'philgeps_posting_ref_no' => $schedule['philgeps_posting_ref_no'] ?? '',
            'ads_post_ib' => $schedule['ads_post_ib'] ?? '',
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? '',
            'list_invited_observers' => $schedule['list_invited_observers'] ?? '',
            'obsrvr_prebid_conf' => $schedule['obsrvr_prebid_conf'] ?? '',
            'obsrvr_eligibility' => $schedule['obsrvr_eligibility'] ?? '',
            'obsrvr_sub_open_of_bid' => $schedule['obsrvr_sub_open_of_bid'] ?? '',
            'obsrvr_bid' => $schedule['obsrvr_bid'] ?? '',
            'obsrvr_post_qual' => $schedule['obsrvr_post_qual'] ?? '',
            'pre_bid_conf' => $schedule['pre_bid_conf'] ?? '',
            'eligibility_check' => $schedule['eligibility_check'] ?? '',
            'sub_open_bids' => $schedule['sub_open_bids'] ?? '',
            'bid_evaluation_date' => $schedule['bid_evaluation_date'] ?? '',
            'post_qualification_date' => $schedule['post_qualification_date'] ?? '',
            'bidding_result' => $schedule['bidding_result'] ?? '',
            'resolution_number_mop' => $schedule['resolution_number_mop'] ?? '',
            'resolution_number' => $schedule['resolution_number'] ?? '',
            'rfq_no' => $schedule['rfq_no'] ?? '',
            'canvass_date' => $schedule['canvass_date'] ?? '',
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? '',
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? '',
        ];
    }



    private function buildPerLotRow(Procurement $procurement, Collection $scheduleMap): array
    {
        $latestMop = $procurement->mopLots()
            ->with('modeOfProcurement')
            ->orderBy('mode_order', 'desc')
            ->first();

        $schedule = [];
        if ($latestMop && $scheduleMap->has($procurement->procID)) {
            $schedule = $scheduleMap[$procurement->procID][$latestMop->uid] ?? [];
        }

        return [
            'procID' => $procurement->procID,
            'prItemID' => null,
            'pr_number' => $procurement->pr_number,
            'procurement_program_project' => $procurement->procurement_program_project,
            'procurement_type' => 'perLot',
            'mop_id' => $latestMop?->id,
            'mop_uid' => $latestMop?->uid,
            'mode_of_procurement_id' => $latestMop?->mode_of_procurement_id,
            'mode_order' => $latestMop?->mode_order ?? 0,

            // Schedule fields
            'bidding_number' => $schedule['bidding_number'] ?? '',
            'ib_number' => $schedule['ib_number'] ?? '',
            'philgeps_posting_ref_no' => $schedule['philgeps_posting_ref_no'] ?? '',
            'ads_post_ib' => $schedule['ads_post_ib'] ?? '',
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? '',
            'list_of_invited_observers' => $schedule['list_of_invited_observers'] ?? '',
            'observer_1' => $schedule['observer_1'] ?? '',
            'observer_2' => $schedule['observer_2'] ?? '',
            'observer_3' => $schedule['observer_3'] ?? '',
            'observer_4' => $schedule['observer_4'] ?? '',
            'observer_5' => $schedule['observer_5'] ?? '',
            'pre_bid_conference' => $schedule['pre_bid_conference'] ?? '',
            'eligibility_check' => $schedule['eligibility_check'] ?? '',
            'submission_opening_of_bids' => $schedule['submission_opening_of_bids'] ?? '',
            'bid_evaluation' => $schedule['bid_evaluation'] ?? '',
            'post_qualification' => $schedule['post_qualification'] ?? '',
            'bidding_result' => $schedule['bidding_result'] ?? '',
            'resolution_number_mop' => $schedule['resolution_number_mop'] ?? '',
            'resolution_number' => $schedule['resolution_number'] ?? '',
            'rfq_no' => $schedule['rfq_no'] ?? '',
            'canvass_date' => $schedule['canvass_date'] ?? '',
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? '',
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? '',
        ];
    }



    private function validateAbcThreshold(): bool
    {
        // Get selected procurements
        $procurements = Procurement::whereIn('procID', $this->selectedItems)->get();

        $hasBelow200k = false;
        $hasAbove200k = false;
        $differentPRs = [];

        foreach ($procurements as $procurement) {
            $abcAmount = $procurement->abc ?? 0;

            if ($abcAmount < 200000) {
                $hasBelow200k = true;
                if ($hasAbove200k) {
                    $differentPRs[] = $procurement->pr_number;
                }
            } else {
                $hasAbove200k = true;
                if ($hasBelow200k) {
                    $differentPRs[] = $procurement->pr_number;
                }
            }
        }

        // If mismatch found
        if ($hasBelow200k && $hasAbove200k) {
            $prList = implode(', ', $differentPRs);
            LivewireAlert::title('ABC Threshold Mismatch')
                ->warning()
                ->text("PR {$prList} " . (count($differentPRs) > 1 ? 'have' : 'has') . " different ABC threshold. All PRs must be below ₱200K or ₱200K and above.")
                ->toast()
                ->position('top-end')
                ->show();
            return false;
        }

        return true;
    }

    public function save()
    {
        if (empty($this->selectedItems)) {
            LivewireAlert::title('No Items Selected')
                ->warning()
                ->text('Please select at least one procurement to edit.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Validate ABC threshold first
        if (!$this->validateAbcThreshold()) {
            return;
        }

        // Validate before starting transaction
        if (!$this->validateBulkSchedules()) {
            $errorMessage = implode(' ', $this->scheduleValidationErrors);
            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()->position('top-end')->show();
            return;
        }

        // Check for permission to modify successful bids
        if (!$this->canModifySuccessfulBids()) {
            $errorMessage = implode(' ', $this->scheduleValidationErrors);
            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()->position('top-end')->show();
            return;
        }

        // Validate against clearing existing data
        if (!$this->validateExistingScheduleDeletion()) {
            $errorMessage = implode(' ', $this->scheduleValidationErrors);
            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()->position('top-end')->show();
            return;
        }

        $isMopAdded = false;
        $isMopUpdated = false;

        DB::transaction(function () use (&$isMopAdded, &$isMopUpdated) {
            // Get selected procurements
            $procurements = Procurement::whereIn('procID', $this->selectedItems)->get();

            foreach ($procurements as $procurement) {
                $currentItems = collect($this->items)
                    ->where('procID', $procurement->procID)
                    ->sortByDesc('mode_order')
                    ->values();

                if ($currentItems->isEmpty()) {
                    continue;
                }

                $currentItem = $currentItems->first();
                $modeId = $this->bulkEdit['mode_of_procurement_id'] ?? null;

                // Check if mode has changed and there's no schedule data - UPDATE mode instead of adding
                $modeHasChanged = $currentItem['mode_of_procurement_id'] != $modeId && $modeId && $modeId != 1;
                $canUpdateMode = $modeHasChanged && !$this->itemHasSchedule($currentItem);

                // Check if we're adding a NEW mode (for rebidding):
                // 1. showAddForm is true (Add button was clicked), OR
                // 2. Current mode is 1 and we're changing to another mode
                $isAddingNewMode = $this->showAddForm ||
                    ($currentItem['mode_of_procurement_id'] == 1 && $modeId && $modeId != 1);

                if ($canUpdateMode) {
                    // Update existing mode_of_procurement_id (no schedule data yet)
                    MopLot::where('id', $currentItem['mop_id'])->update([
                        'mode_of_procurement_id' => $modeId,
                    ]);
                    $isMopUpdated = true;
                } elseif ($isAddingNewMode && $modeId && $modeId != 1) {
                    $modeOrder = ($currentItem['mode_order'] ?? 0) + 1;
                    $generatedUid = "MOP-{$modeId}-{$modeOrder}";

                    $savedMop = MopLot::create([
                        'procID' => $procurement->procID,
                        'uid' => $generatedUid,
                        'mode_of_procurement_id' => $modeId,
                        'mode_order' => $modeOrder,
                    ]);
                    $isMopAdded = true;

                    // Save schedules for the new mode
                    $this->updatePerLotSchedules([
                        'mop_uid' => $generatedUid,
                        'procID' => $procurement->procID,
                        'mode_of_procurement_id' => $modeId,
                    ]);
                } else {
                    // Update existing mode schedules
                    $this->updateItem($currentItem);
                    $isMopUpdated = true;
                }
            }
        });

        // Show appropriate success message
        if ($isMopAdded) {
            LivewireAlert::title('Mode Added Successfully!')->success()->text('The mode of procurement has been added.')->toast()->position('top-end')->show();
        } elseif ($isMopUpdated) {
            LivewireAlert::title('Updates Saved!')->success()->text('Changes have been saved successfully.')->toast()->position('top-end')->show();
        } else {
            LivewireAlert::title('No Changes')->info()->text('No changes were detected.')->toast()->position('top-end')->show();
        }

        // Reload data instead of redirecting
        $this->loadProcurementData();
        $this->populateBulkEditData();
        $this->loadPostProcurementData();
        $this->showAddForm = false;
        $this->showBulkEditModal = false;
        $this->clearSelections();
    }

    private function updateItem(array $item): void
    {
        $this->updatePerLotSchedules($item);
    }

    private function updatePerLotSchedules(array $item): void
    {
        if (!$item['mop_uid']) {
            return;
        }

        $modeId = $item['mode_of_procurement_id'];
        $refId = $item['procID'];
        $mopUid = $item['mop_uid'];

        // COMPETITIVE BIDDING MODES (2-6): Only save BidSchedule
        if (in_array($modeId, [2, 3, 4, 5, 6])) {
            BidSchedule::updateOrCreate(
                ['mop_uid' => $mopUid, 'ref_id' => $refId],
                [
                    'bidding_number' => $this->bulkEdit['bidding_number'] ?? null,
                    'ib_number' => $this->bulkEdit['ib_number'] ?? null,
                    'philgeps_posting_ref_no' => $this->bulkEdit['philgeps_posting_ref_no'] ?? null,
                    'ads_post_ib' => $this->nullableDate($this->bulkEdit['ads_post_ib'] ?? ''),
                    'pre_proc_conference' => $this->nullableDate($this->bulkEdit['pre_proc_conference'] ?? ''),
                    'list_invited_observers' => $this->nullableDate($this->bulkEdit['list_invited_observers'] ?? ''),
                    'obsrvr_prebid_conf' => $this->nullableDate($this->bulkEdit['obsrvr_prebid_conf'] ?? ''),
                    'obsrvr_eligibility' => $this->nullableDate($this->bulkEdit['obsrvr_eligibility'] ?? ''),
                    'obsrvr_sub_open_of_bid' => $this->nullableDate($this->bulkEdit['obsrvr_sub_open_of_bid'] ?? ''),
                    'obsrvr_bid' => $this->nullableDate($this->bulkEdit['obsrvr_bid'] ?? ''),
                    'obsrvr_post_qual' => $this->nullableDate($this->bulkEdit['obsrvr_post_qual'] ?? ''),
                    'pre_bid_conf' => $this->nullableDate($this->bulkEdit['pre_bid_conf'] ?? ''),
                    'eligibility_check' => $this->nullableDate($this->bulkEdit['eligibility_check'] ?? ''),
                    'sub_open_bids' => $this->nullableDate($this->bulkEdit['sub_open_bids'] ?? ''),
                    'bid_evaluation_date' => $this->nullableDate($this->bulkEdit['bid_evaluation_date'] ?? ''),
                    'post_qualification_date' => $this->nullableDate($this->bulkEdit['post_qualification_date'] ?? ''),
                    'bidding_result' => $this->bulkEdit['bidding_result'] ?? null,
                    'resolution_number_mop' => $this->bulkEdit['resolution_number_mop'] ?? null,
                ]
            );
            // Delete any SVP records for this procurement (mode changed from SVP to bidding)
            PrSvp::where('mop_uid', $mopUid)->where('ref_id', $refId)->delete();
        }

        // SVP/ALTERNATIVE MODES (7-24): Only save PrSvp
        if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
            // Get ABC to determine required fields
            $procurement = Procurement::find($refId);
            $abc = $procurement ? $procurement->abc : 0;

            // Base SVP data
            $svpData = [
                'resolution_number_mop' => $this->bulkEdit['resolution_number_mop'] ?? null,
                'rfq_no' => $this->bulkEdit['rfq_no'] ?? null,
                'canvass_date' => $this->nullableDate($this->bulkEdit['canvass_date'] ?? ''),
                'date_returned_of_canvass' => $this->nullableDate($this->bulkEdit['date_returned_of_canvass'] ?? ''),
                'abstract_of_canvass_date' => $this->nullableDate($this->bulkEdit['abstract_of_canvass_date'] ?? ''),
            ];

            // For ABC 200k and above, also save PhilGEPS fields
            if ($abc >= 200000) {
                $svpData['philgeps_posting_ref_no'] = $this->bulkEdit['philgeps_posting_ref_no'] ?? null;
                $svpData['ads_post_ib'] = $this->nullableDate($this->bulkEdit['ads_post_ib'] ?? '');
            }

            PrSvp::updateOrCreate(
                ['mop_uid' => $mopUid, 'ref_id' => $refId],
                $svpData
            );
            // Delete any BidSchedule records for this procurement (mode changed from bidding to SVP)
            BidSchedule::where('mop_uid', $mopUid)->where('ref_id', $refId)->delete();
        }
    }



    private function nullableDate($value)
    {
        return $this->hasValue($value) ? $value : null;
    }

    public function toggleHistory(string $key)
    {
        if ($this->historyForKey === $key && $this->showHistory) {
            $this->showHistory = false;
            $this->historyForKey = null;
        } else {
            $this->showHistory = true;
            $this->historyForKey = $key;
        }
    }

    public function getHistoryItemsProperty()
    {
        if (!$this->showHistory || !$this->historyForKey) {
            return collect();
        }

        // Parse the key to get procID
        $parts = explode('_', $this->historyForKey);
        $type = $parts[0]; // 'lot'
        $id = $parts[1];

        // Get all items for this PR except the first (current) one
        return collect($this->items)
            ->filter(function ($item) use ($id) {
                return $item['procurement_type'] === 'perLot' && $item['procID'] == $id;
            })
            ->skip(1) // Skip the current/first mode
            ->values();
    }

    private function formatDate($date): string
    {
        if (empty($date)) {
            return '-';
        }

        try {
            return Carbon::parse($date)->format('m/d/Y');
        } catch (\Exception $e) {
            return $date;
        }
    }

    public function setStep(int $step): void
    {
        if ($step == 2 && !$this->isPostAvailable) {
            LivewireAlert::title('Cannot Access Post Tab')
                ->warning()
                ->text('Post-procurement tab is not yet available. Please complete the bulk edit first.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $this->activeTab = $step;
    }

    public function getIsPostAvailableProperty(): bool
    {
        $currentItems = $this->getCurrentItems();

        if (empty($currentItems)) {
            return false;
        }

        // Check if ALL items meet post-procurement criteria
        foreach ($currentItems as $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;

            // COMPETITIVE BIDDING MODES (2, 3, 4, 5, 6)
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                // Check all required bidding fields are filled
                $allBiddingFieldsFilled =
                    $this->hasValue($item['bidding_number']) &&
                    $this->hasValue($item['ib_number']) &&
                    $this->hasValue($item['philgeps_posting_ref_no']) &&
                    $this->hasValue($item['ads_post_ib']) &&
                    $this->hasValue($item['pre_proc_conference']) &&
                    $this->hasValue($item['list_invited_observers']) &&
                    $this->hasValue($item['obsrvr_prebid_conf']) &&
                    $this->hasValue($item['obsrvr_eligibility']) &&
                    $this->hasValue($item['obsrvr_sub_open_of_bid']) &&
                    $this->hasValue($item['obsrvr_bid']) &&
                    $this->hasValue($item['obsrvr_post_qual']) &&
                    $this->hasValue($item['pre_bid_conf']) &&
                    $this->hasValue($item['eligibility_check']) &&
                    $this->hasValue($item['sub_open_bids']) &&
                    $this->hasValue($item['bid_evaluation_date']) &&
                    $this->hasValue($item['post_qualification_date']) &&
                    $this->hasValue($item['sub_open_bids']) &&
                    $this->hasValue($item['bidding_result']) &&
                    ($item['bidding_result'] === 'SUCCESSFUL');

                // For modes 2-6, also require resolution_number_mop
                if (in_array($modeId, [2, 3, 4, 5, 6])) {
                    $allBiddingFieldsFilled = $allBiddingFieldsFilled && $this->hasValue($item['resolution_number_mop']);
                }

                if ($allBiddingFieldsFilled) {
                    return true;
                }
            }

            // SVP/ALTERNATIVE MODES (7-24)
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                // Get ABC from the procurement
                $procurement = \App\Models\Procurement::where('procID', $item['procID'])->first();
                $abc = $procurement ? $procurement->abc : 0;

                // Base required SVP fields
                $allSvpFieldsFilled =
                    $this->hasValue($item['resolution_number_mop']) &&
                    $this->hasValue($item['rfq_no']) &&
                    $this->hasValue($item['canvass_date']) &&
                    $this->hasValue($item['date_returned_of_canvass']) &&
                    $this->hasValue($item['abstract_of_canvass_date']);

                // If ABC is 200k or above, also require philgeps_posting_ref_no and ads_post_ib
                if ($abc >= 200000) {
                    $allSvpFieldsFilled = $allSvpFieldsFilled &&
                        $this->hasValue($item['philgeps_posting_ref_no']) &&
                        $this->hasValue($item['ads_post_ib']);
                }

                if ($allSvpFieldsFilled) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getDisableModeSelectProperty(): bool
    {
        // If we're in "add mode", always enable the dropdown
        if ($this->showAddForm) {
            return false;
        }

        $currentItems = $this->getCurrentItems();

        if (empty($currentItems)) {
            return false;
        }

        // Disable if ANY item has schedule data
        foreach ($currentItems as $item) {
            if ($this->itemHasSchedule($item)) {
                return true;
            }
        }

        return false;
    }

    public function getShowAddModeButtonProperty(): bool
    {
        // Don't show button if form is already shown
        if ($this->showAddForm) {
            return false;
        }

        $currentItems = $this->getCurrentItems();

        if (empty($currentItems)) {
            return false;
        }

        // Check if ALL items meet the criteria to show Add button
        $allCanAdd = true;

        foreach ($currentItems as $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;

            // Can't add for SVP modes
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                $allCanAdd = false;
                break;
            }

            $bidResult = $item['bidding_result'] ?? '';

            $hasBiddingData = $this->hasValue($item['ib_number']) &&
                $this->hasValue($item['bidding_number']) &&
                $this->hasValue($item['sub_open_bids']);

            $hasPreProcConference = $this->hasValue($item['pre_proc_conference']);

            // Item can add if: mode_id = 1 OR (has bidding data/pre-proc AND result is UNSUCCESSFUL)
            $itemCanAdd = $modeId == 1 ||
                (($hasBiddingData || $hasPreProcConference) &&
                    $bidResult === 'UNSUCCESSFUL');

            if (!$itemCanAdd) {
                $allCanAdd = false;
                break;
            }
        }

        return $allCanAdd;
    }

    public function getShowBiddingFieldsProperty(): bool
    {
        $modeId = $this->bulkEdit['mode_of_procurement_id'] ?? null;
        return in_array($modeId, [2, 3, 4, 5, 6]);
    }

    public function getShowSvpFieldsProperty(): bool
    {
        $modeId = $this->bulkEdit['mode_of_procurement_id'] ?? null;
        return in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]);
    }

    public function getDisableInputsProperty(): bool
    {
        // Disable inputs if ALL selected PRs have:
        // 1. SUCCESSFUL bidding result
        // 2. Post-procurement data exists
        // 3. User doesn't have edit permission

        $canEditMop = auth()->user()->can('edit_mode::of::procurement');

        \Log::info('Bulk Edit - Checking disable inputs', [
            'user_has_permission' => $canEditMop,
            'user_id' => auth()->id(),
        ]);

        if ($canEditMop) {
            \Log::info('Bulk Edit - Not disabling: User has permission');
            return false; // User has permission, don't disable
        }

        $currentItems = $this->getCurrentItems();

        if (empty($currentItems)) {
            \Log::info('Bulk Edit - Not disabling: No current items found');
            return false;
        }

        \Log::info('Bulk Edit - Checking items', ['count' => count($currentItems)]);

        // Check if ALL items meet the disable criteria
        foreach ($currentItems as $index => $item) {
            $biddingResult = $item['bidding_result'] ?? '';
            $refId = $item['procurement_type'] === 'perLot' ? $item['procID'] : $item['prItemID'];

            $hasPostData = \App\Models\PostProcurement::where('ref_id', $refId)->exists();
            $isSuccessful = $biddingResult === 'SUCCESSFUL';

            \Log::info("Bulk Edit - Item $index check", [
                'pr_number' => $item['pr_number'] ?? 'N/A',
                'procurement_type' => $item['procurement_type'],
                'ref_id' => $refId,
                'bidding_result' => $biddingResult,
                'is_successful' => $isSuccessful,
                'has_post_data' => $hasPostData,
                'meets_criteria' => $isSuccessful && $hasPostData,
            ]);

            // If ANY item doesn't meet criteria, don't disable
            if (!($isSuccessful && $hasPostData)) {
                \Log::info("Bulk Edit - Not disabling: Item $index doesn't meet criteria");
                return false;
            }
        }

        // All items are successful with post data and user lacks permission
        \Log::info('Bulk Edit - DISABLING: All items meet criteria and user lacks permission');
        return true;
    }

    public function getAbcThresholdCategoryProperty(): string
    {
        $procurements = Procurement::whereIn('procID', $this->procurementIds)->get();

        if ($procurements->isEmpty()) {
            return 'N/A';
        }

        // Check the first procurement's ABC to determine category
        $firstAbc = $procurements->first()->abc ?? 0;

        if ($firstAbc < 200000) {
            return 'Below ₱200,000.00';
        } else {
            return '₱200,000.00 and Above';
        }
    }

    public function savePost()
    {
        // First check if Post tab should be accessible
        if (!$this->isPostAvailable) {
            LivewireAlert::title('Invalid Action')
                ->error()
                ->text('Cannot save post-procurement data. Prerequisites not met.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $postFields = [
            $this->resolutionAwardNumber,
            $this->resolutionAwardDate,
            $this->noticeOfAwardNumber,
            $this->noticeOfAward,
            $this->awardedAmount,
            $this->philgepsNoticeOfAwardNo,
            $this->philgepsPostingOfAward,
            $this->supplier_id,
        ];

        $hasAnyPostData = $this->hasAnyValue($postFields);

        // Define base validation rules (all nullable by default)
        $rules = [
            'resolutionAwardNumber' => 'nullable|string|max:255',
            'resolutionAwardDate' => 'nullable|date',
            'noticeOfAwardNumber' => 'nullable|string|max:255',
            'noticeOfAward' => 'nullable|date',
            'awardedAmount' => 'nullable|numeric|min:0',
            'philgepsNoticeOfAwardNo' => 'nullable|string|max:255',
            'philgepsPostingOfAward' => 'nullable|date',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
        ];

        // If ANY field has data, make Resolution Award Number and Date REQUIRED
        if ($hasAnyPostData) {
            $rules['resolutionAwardNumber'] = 'required|string|max:255';
            $rules['resolutionAwardDate'] = 'required|date';
        }

        $attributes = [
            'resolutionAwardNumber' => 'Resolution Award Number',
            'resolutionAwardDate' => 'Resolution Award Date',
            'noticeOfAwardNumber' => 'Notice of Award Number',
            'noticeOfAward' => 'Notice of Award Date',
            'awardedAmount' => 'Awarded Amount',
            'philgepsNoticeOfAwardNo' => 'PhilGEPS Notice of Award Number',
            'philgepsPostingOfAward' => 'PhilGEPS Posting of Award',
            'supplier_id' => 'Supplier',
        ];

        // Custom error messages for better UX
        $messages = [
            'resolutionAwardNumber.required' => 'Resolution Award Number is required when entering post-procurement data.',
            'resolutionAwardDate.required' => 'Resolution Award Date is required when entering post-procurement data.',
        ];

        try {
            $this->validate($rules, $messages, $attributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = $e->validator->errors()->all();
            $errorString = implode(' ', $errorMessages);

            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorString)
                ->toast()->position('top-end')->show();

            return; // Exit early on validation failure
        }

        // For bulk edit, apply post-procurement to ALL current items
        $currentItems = $this->getCurrentItems();
        $isAdded = false;
        $isUpdated = false;
        $updatedCount = 0;
        $addedCount = 0;

        DB::transaction(function () use ($currentItems, &$isAdded, &$isUpdated, &$addedCount, &$updatedCount) {
            foreach ($currentItems as $item) {
                $refId = $item['procurement_type'] === 'perLot' ? $item['procID'] : $item['prItemID'];

                $data = [
                    'ref_id' => $refId,
                    'resolution_award_number' => $this->resolutionAwardNumber,
                    'resolution_award_date' => $this->resolutionAwardDate,
                    'notice_of_award_number' => $this->noticeOfAwardNumber,
                    'notice_of_award' => $this->noticeOfAward,
                    'awarded_amount' => $this->awardedAmount,
                    'philgeps_notice_of_award_no' => $this->philgepsNoticeOfAwardNo,
                    'philgeps_posting_of_award' => $this->nullableDate($this->philgepsPostingOfAward),
                    'supplier_id' => $this->supplier_id,
                ];

                $postModel = \App\Models\PostProcurement::updateOrCreate(
                    ['ref_id' => $refId],
                    $data
                );

                if ($postModel->wasRecentlyCreated) {
                    $isAdded = true;
                    $addedCount++;
                } elseif ($postModel->wasChanged()) {
                    $isUpdated = true;
                    $updatedCount++;
                }
            }
        });

        if ($isAdded) {
            LivewireAlert::title('Post-Procurement Added!')
                ->success()
                ->text("The procurement award details have been saved for {$addedCount} PR(s).")
                ->toast()
                ->position('top-end')
                ->show();
        } elseif ($isUpdated) {
            LivewireAlert::title('Post-Procurement Updated!')
                ->success()
                ->text("The procurement award details have been updated for {$updatedCount} PR(s).")
                ->toast()
                ->position('top-end')
                ->show();
        } else {
            LivewireAlert::title('No Changes')
                ->info()
                ->text('Post-procurement details remain unchanged.')
                ->toast()
                ->position('top-end')
                ->show();
        }

        // Reload data
        $this->loadProcurementData();
        $this->populateBulkEditData();
        $this->loadPostProcurementData();
    }

    public function cancel()
    {
        // Preserve query parameters (filters, pagination) when returning to index
        $queryParams = $this->queryParams;
        unset($queryParams['items']); // Remove items param

        return redirect()->route('mode-of-procurement.index', $queryParams);
    }

    /**
     * Add a new mode/rebid for all selected PRs
     * Creates new MOP records with proper mode_order indexing
     */
    public function addItem(): void
    {
        // Validate that all PRs can accept a new mode
        $currentItems = $this->getCurrentItems();

        if (empty($currentItems)) {
            LivewireAlert::title('Error')
                ->error()
                ->text('No procurement items found.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Check if ALL PRs can add rebid
        foreach ($currentItems as $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;

            // Cannot rebid SVP modes
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                LivewireAlert::title('Cannot Add Rebid')
                    ->warning()
                    ->text('Cannot add rebid for SVP/Alternative modes. PR: ' . $item['pr_number'])
                    ->toast()
                    ->position('top-end')
                    ->show();
                return;
            }

            $bidResult = $item['bidding_result'] ?? '';
            $hasBiddingData = $this->hasValue($item['ib_number']) &&
                $this->hasValue($item['bidding_number']) &&
                $this->hasValue($item['sub_open_bids']);
            $hasPreProcConference = $this->hasValue($item['pre_proc_conference']);

            // Can add if: mode_id = 1 OR (has bidding data/pre-proc AND result is UNSUCCESSFUL)
            $canAdd = $modeId == 1 ||
                (($hasBiddingData || $hasPreProcConference) && $bidResult === 'UNSUCCESSFUL');

            if (!$canAdd) {
                LivewireAlert::title('Cannot Add Rebid')
                    ->warning()
                    ->text('PR ' . $item['pr_number'] . ' does not meet requirements for adding a new mode.')
                    ->toast()
                    ->position('top-end')
                    ->show();
                return;
            }
        }

        // All validations passed - enable the add form
        $this->bulkEdit['mode_of_procurement_id'] = null;
        $this->clearBulkEditScheduleFields();
        $this->showAddForm = true;
    }

    /**
     * Clear all schedule fields in bulk edit form
     */
    private function clearBulkEditScheduleFields(): void
    {
        // Clear bidding fields
        $this->bulkEdit['bidding_number'] = '';
        $this->bulkEdit['ib_number'] = '';
        $this->bulkEdit['philgeps_posting_ref_no'] = '';
        $this->bulkEdit['ads_post_ib'] = '';
        $this->bulkEdit['pre_proc_conference'] = '';
        $this->bulkEdit['list_invited_observers'] = '';
        $this->bulkEdit['obsrvr_prebid_conf'] = '';
        $this->bulkEdit['obsrvr_eligibility'] = '';
        $this->bulkEdit['obsrvr_sub_open_of_bid'] = '';
        $this->bulkEdit['obsrvr_bid'] = '';
        $this->bulkEdit['obsrvr_post_qual'] = '';
        $this->bulkEdit['pre_bid_conf'] = '';
        $this->bulkEdit['eligibility_check'] = '';
        $this->bulkEdit['sub_open_bids'] = '';
        $this->bulkEdit['bid_evaluation_date'] = '';
        $this->bulkEdit['post_qualification_date'] = '';
        $this->bulkEdit['bidding_result'] = '';
        $this->bulkEdit['resolution_number_mop'] = '';

        // Clear SVP fields
        $this->bulkEdit['resolution_number'] = '';
        $this->bulkEdit['rfq_no'] = '';
        $this->bulkEdit['canvass_date'] = '';
        $this->bulkEdit['date_returned_of_canvass'] = '';
        $this->bulkEdit['abstract_of_canvass_date'] = '';
    }

    // Post-Procurement Bulk Edit Methods
    public function updatedSelectAllPost($value)
    {
        if ($value) {
            $eligibleItems = [];
            $processed = [];

            foreach ($this->items as $item) {
                $refId = $item['procurement_type'] === 'perLot'
                    ? $item['procID']
                    : $item['prItemID'];

                // Skip duplicates
                if (in_array($refId, $processed)) {
                    continue;
                }
                $processed[] = $refId;

                // Check eligibility
                $bidSchedule = \App\Models\BidSchedule::where('ref_id', $refId)->first();
                $prSvp = \App\Models\PrSvp::where('ref_id', $refId)->first();
                $isBiddingSuccessful = $bidSchedule && $bidSchedule->bidding_result === 'SUCCESSFUL';
                $hasSvpData = $prSvp && ($prSvp->negotiated_contract_amount || $prSvp->canvasser_id);

                if ($isBiddingSuccessful || $hasSvpData) {
                    $eligibleItems[] = $refId;
                }
            }

            $this->selectedPostItems = $eligibleItems;
        } else {
            $this->selectedPostItems = [];
        }
        $this->dispatch('$refresh');
    }

    public function updatedSelectedPostItems()
    {
        $eligibleItems = [];
        $processed = [];

        foreach ($this->items as $item) {
            $refId = $item['procurement_type'] === 'perLot'
                ? $item['procID']
                : $item['prItemID'];

            if (in_array($refId, $processed)) {
                continue;
            }
            $processed[] = $refId;

            $bidSchedule = \App\Models\BidSchedule::where('ref_id', $refId)->first();
            $prSvp = \App\Models\PrSvp::where('ref_id', $refId)->first();
            $isBiddingSuccessful = $bidSchedule && $bidSchedule->bidding_result === 'SUCCESSFUL';
            $hasSvpData = $prSvp && ($prSvp->negotiated_contract_amount || $prSvp->canvasser_id);

            if ($isBiddingSuccessful || $hasSvpData) {
                $eligibleItems[] = $refId;
            }
        }

        $selectedUnique = array_unique($this->selectedPostItems);
        $this->selectAllPost = !empty($eligibleItems) &&
            count($selectedUnique) === count($eligibleItems) &&
            empty(array_diff($selectedUnique, $eligibleItems));
        $this->dispatch('$refresh');
    }

    public function clearPostSelections()
    {
        $this->selectedPostItems = [];
        $this->selectAllPost = false;
    }

    public function openPostBulkEditModal()
    {
        if (empty($this->selectedPostItems)) {
            LivewireAlert::title('No Items Selected')
                ->warning()
                ->text('Please select at least one procurement to bulk edit.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Prepare selected items data for display
        $selectedItemsData = [];
        foreach ($this->items as $item) {
            $refId = $item['procurement_type'] === 'perLot'
                ? $item['procID']
                : $item['prItemID'];

            if (in_array($refId, $this->selectedPostItems)) {
                $selectedItemsData[] = [
                    'ref_id' => $refId,
                    'pr_number' => $item['pr_number'],
                    'procurement_program_project' => $item['procurement_program_project'],
                ];
            }
        }

        $this->postBulkEditData = [
            'selected_items' => array_values(array_unique($selectedItemsData, SORT_REGULAR)),
            'resolutionAwardNumber' => '',
            'resolutionAwardDate' => '',
            'noticeOfAwardNumber' => '',
            'noticeOfAward' => '',
            'awardedAmount' => null,
            'philgepsNoticeOfAwardNo' => '',
            'philgepsPostingOfAward' => '',
            'supplier_id' => null,
        ];

        $this->showPostBulkEditModal = true;
    }

    public function closePostBulkEditModal()
    {
        $this->showPostBulkEditModal = false;
        $this->postBulkEditData = [];
    }

    public function savePostBulkEdit()
    {
        if (empty($this->postBulkEditData['selected_items'])) {
            LivewireAlert::title('No Items Selected')
                ->warning()
                ->text('Please select items to update.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $updatedCount = 0;

        try {
            DB::beginTransaction();

            foreach ($this->postBulkEditData['selected_items'] as $item) {
                $refId = $item['ref_id'];

                // Find or create post-procurement record
                $postProc = \App\Models\PostProcurement::firstOrNew(['ref_id' => $refId]);

                $hasChanges = false;

                // Update only non-empty fields
                if (!empty($this->postBulkEditData['resolutionAwardNumber'])) {
                    $postProc->resolution_award_number = $this->postBulkEditData['resolutionAwardNumber'];
                    $hasChanges = true;
                }
                if (!empty($this->postBulkEditData['resolutionAwardDate'])) {
                    $postProc->resolution_award_date = $this->postBulkEditData['resolutionAwardDate'];
                    $hasChanges = true;
                }
                if (!empty($this->postBulkEditData['noticeOfAwardNumber'])) {
                    $postProc->notice_of_award_number = $this->postBulkEditData['noticeOfAwardNumber'];
                    $hasChanges = true;
                }
                if (!empty($this->postBulkEditData['noticeOfAward'])) {
                    $postProc->notice_of_award = $this->postBulkEditData['noticeOfAward'];
                    $hasChanges = true;
                }
                if (!empty($this->postBulkEditData['awardedAmount'])) {
                    $postProc->awarded_amount = $this->postBulkEditData['awardedAmount'];
                    $hasChanges = true;
                }
                if (!empty($this->postBulkEditData['philgepsNoticeOfAwardNo'])) {
                    $postProc->philgeps_notice_of_award_no = $this->postBulkEditData['philgepsNoticeOfAwardNo'];
                    $hasChanges = true;
                }
                if (!empty($this->postBulkEditData['philgepsPostingOfAward'])) {
                    $postProc->philgeps_posting_of_award = $this->postBulkEditData['philgepsPostingOfAward'];
                    $hasChanges = true;
                }
                if (!empty($this->postBulkEditData['supplier_id'])) {
                    $postProc->supplier_id = $this->postBulkEditData['supplier_id'];
                    $hasChanges = true;
                }

                if ($hasChanges) {
                    $postProc->save();
                    $updatedCount++;
                }
            }

            DB::commit();

            LivewireAlert::title('Success!')
                ->success()
                ->text("{$updatedCount} post-procurement record(s) updated successfully.")
                ->toast()
                ->position('top-end')
                ->show();

            $this->closePostBulkEditModal();
            $this->loadProcurementData();

        } catch (\Exception $e) {
            DB::rollBack();

            LivewireAlert::title('Error!')
                ->error()
                ->text('Failed to update post-procurement data: ' . $e->getMessage())
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-bulk-edit-per-lot-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
            'suppliers' => $this->suppliers,
            'isPostAvailable' => $this->isPostAvailable,
            'disableInputs' => $this->disableInputs,
            'disableModeSelect' => $this->disableModeSelect,
            'showBiddingFields' => $this->showBiddingFields,
            'showSvpFields' => $this->showSvpFields,
            'abcThresholdCategory' => $this->abcThresholdCategory,
            'showAddModeButton' => $this->showAddModeButton,
            'showAddForm' => $this->showAddForm,
        ]);
    }
}
