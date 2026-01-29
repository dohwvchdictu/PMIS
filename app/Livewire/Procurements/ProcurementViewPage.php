<?php

namespace App\Livewire\Procurements;

use Livewire\Component;
use App\Models\{
    Procurement,
    Category,
    Division,
    ClusterCommittee,
    VenueSpecific,
    ProvinceHuc,
    EndUser,
    FundSource,
    ModeOfProcurement,
    Supplier,
    PostProcurement,
    BidSchedule,
    PrSvp
};
use Illuminate\Support\Collection;

class ProcurementViewPage extends Component
{
    public Procurement $procurement;
    public array $form = [];
    public int $activeTab = 1;
    public bool $showTable = true;
    public int $textareaRows = 1;

    // Reference data
    public Collection $categories;
    public Collection $divisions;
    public Collection $clusterCommittees;
    public Collection $venueSpecifics;
    public Collection $venueProvinces;
    public Collection $endUsers;
    public Collection $fundSources;
    public Collection $modeOfProcurements;
    public Collection $suppliers;

    // Pagination
    public int $page = 1;
    public int $perPage = 10;
    public int $postPage = 1;
    public int $postPerPage = 10;
    public string $mopSearchTerm = '';
    public string $postSearchTerm = '';
    public $mopToggles = [];
    public array $postItems = [];
    public bool $showModal = false;
    public ?array $selectedSupplier = null;

    // Post procurement data
    public ?string $resolutionNumber = null;
    public ?string $bidEvaluationDate = null;
    public ?string $postQualDate = null;
    public ?string $noticeOfAwardNumber = null;
    public ?string $noticeOfAward = null;
    public ?string $recommendingForAward = null;
    public ?float $awardedAmount = null;
    public ?string $philgepsReferenceNo = null;
    public ?string $awardNoticeNumber = null;
    public ?string $dateOfPostingOfAwardOnPhilGEPS = null;
    public ?int $supplier_id = null;

    public function mount(Procurement $procurement): void
    {
        $procurement->load([
            'pr_items' => function ($query) {
                $query->with(['prstage.stage', 'currentItemRemark.remark']);
            },
            'category.categoryType',
            'category.bacType',
            'mopLots.modeOfProcurement',
            'bacApprovedPr',
            'currentPrStage.procurementStage',
            'currentLotRemark.remark'
        ]);

        $this->procurement = $procurement;

        // Load basic procurement data
        $this->form = $procurement->toArray();
        $this->form['category_type'] = $procurement->category?->categoryType?->category_type ?? null;
        $this->form['rbac_sbac'] = $procurement->category?->bacType?->abbreviation ?? null;

        // Normalize procurement_type
        if (!in_array($this->form['procurement_type'] ?? null, ['perItem', 'perLot'])) {
            $this->form['procurement_type'] = 'perLot';
        }

        // Load items if perItem
        if ($this->form['procurement_type'] === 'perItem') {
            $this->form['items'] = $procurement->pr_items
                ->sortByDesc('id')
                ->map(fn($item) => [
                    'prItemID' => $item->prItemID,
                    'item_no' => $item->item_no,
                    'description' => $item->description,
                    'amount' => $item->amount ?? 0,
                    'stage' => $item->prstage?->stage?->procurementstage ?? null,
                    'remark' => $item->currentItemRemark?->remark?->remarks ?? null,
                ])
                ->values()
                ->toArray();
        }

        // Load MOP data
        $this->loadMopData($procurement);

        // Load post-procurement data
        $this->loadPostProcurementData($procurement);

        // Load reference data
        $this->loadReferenceData();

        // Calculate textarea rows
        $this->calculateTextareaRows($procurement->procurement_program_project ?? '');
    }

    protected function calculateTextareaRows(string $text): void
    {
        $text = trim($text);
        $lineCount = substr_count($text, "\n") + 1;
        $approxExtraLines = ceil(strlen($text) / 150);
        $this->textareaRows = max($lineCount, $approxExtraLines, 1);
    }

    protected function loadMopData(Procurement $procurement): void
    {
        if ($this->form['procurement_type'] === 'perLot') {
            $this->loadPerLotMopData($procurement);
        } else {
            $this->loadPerItemMopData($procurement);
        }
    }
    protected function loadPerLotMopData(Procurement $procurement): void
    {
        $mopLots = $procurement->mopLots()
            ->with('modeOfProcurement')
            ->orderBy('mode_order')
            ->get();

        $uids = $mopLots->pluck('uid')->filter()->toArray();
        $procID = $procurement->procID;

        if (empty($uids)) {
            $this->form['mop_items'] = [];
            return;
        }

        // Fetch all schedules in batch queries
        $bidSchedules = BidSchedule::whereIn('mop_uid', $uids)
            ->where('ref_id', $procID)
            ->get()
            ->keyBy('mop_uid');

        $prSvps = PrSvp::whereIn('mop_uid', $uids)
            ->where('ref_id', $procID)
            ->get()
            ->keyBy('mop_uid');

        // Build unified schedule map
        $scheduleMap = $this->buildScheduleMap($bidSchedules, $prSvps);

        // Map items
        $this->form['mop_items'] = $mopLots->map(function ($mopLot) use ($scheduleMap) {
            $uid = $mopLot->uid;
            $schedule = $uid ? $scheduleMap->get($uid, []) : [];

            return $this->buildMopItemArray($mopLot, $schedule);
        })->toArray();
    }

    protected function loadPerItemMopData(Procurement $procurement): void
    {
        // Load MOP Items grouped by PR Item ID
        $mopItemsGrouped = $procurement->mopItems()
            ->with(['item', 'modeOfProcurement'])
            ->orderBy('mode_order', 'desc')
            ->get()
            ->groupBy('prItemID');

        // Get prItemIDs for schedules
        $prItemIds = $procurement->pr_items->pluck('prItemID')->filter()->toArray();

        if (empty($prItemIds)) {
            $this->form['items'] = [];
            return;
        }

        // Fetch Schedules by ref_id (prItemID)
        $bidSchedules = BidSchedule::whereIn('ref_id', $prItemIds)->get();
        $prSvps = PrSvp::whereIn('ref_id', $prItemIds)->get();

        // Build unified schedule map keyed by prItemID and mop_uid
        $scheduleMap = $this->buildPerItemScheduleMap($bidSchedules, $prSvps);

        $this->form['items'] = [];

        // Loop through PR Items
        $sortedPrItems = $procurement->pr_items->sortBy('prItemID');

        foreach ($sortedPrItems as $prItem) {
            $prItemID = $prItem->prItemID;
            $relatedMops = $mopItemsGrouped->get($prItemID);

            if ($relatedMops && $relatedMops->count() > 0) {
                foreach ($relatedMops as $mopItem) {
                    // **FIX: Filter out MOP-1-1 entries**
                    if ($mopItem->uid === 'MOP-1-1') {
                        continue;
                    }

                    $uid = $mopItem->uid;
                    // Get schedule for this specific item
                    $schedule = [];
                    if ($uid && $scheduleMap->has($prItemID)) {
                        $prItemSchedules = $scheduleMap->get($prItemID);
                        $schedule = $prItemSchedules->get($uid, []);
                    }
                    $this->form['items'][] = $this->mapPerItemToRow($prItem, $mopItem, $schedule);
                }
            }
        }
    }
    private function buildScheduleMap(Collection $bidSchedules, Collection $prSvps): Collection
    {
        $map = collect();

        foreach ($bidSchedules as $uid => $schedule) {
            $map[$uid] = [
                'ib_number' => $schedule->ib_number,
                'philgeps_posting_ref_no' => $schedule->philgeps_posting_ref_no,
                'pre_proc_conference' => $schedule->pre_proc_conference,
                'ads_post_ib' => $schedule->ads_post_ib,
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
                'bidding_number' => $schedule->bidding_number,
                'bidding_date' => $schedule->bidding_date,
                'bidding_result' => $schedule->bidding_result,
                'resolution_number_mop' => $schedule->resolution_number_mop,
            ];
        }

        foreach ($prSvps as $uid => $schedule) {
            $existing = $map->get($uid, []);
            $map[$uid] = array_merge($existing, [
                'rfq_no' => $schedule->rfq_no,
                'canvass_date' => $schedule->canvass_date,
                'date_returned_of_canvass' => $schedule->date_returned_of_canvass,
                'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date,
                'resolution_number' => $schedule->resolution_number,
            ]);
        }

        return $map;
    }
    private function buildPerItemScheduleMap(Collection $bidSchedules, Collection $prSvps): Collection
    {
        $map = collect();

        // Initialize map for all schedules
        foreach ($bidSchedules as $schedule) {
            $refId = $schedule->ref_id;
            if (!$map->has($refId)) {
                $map[$refId] = collect();
            }
            $map[$refId][$schedule->mop_uid] = [
                'mop_uid' => $schedule->mop_uid,
                'ib_number' => $schedule->ib_number,
                'philgeps_posting_ref_no' => $schedule->philgeps_posting_ref_no,
                'pre_proc_conference' => $schedule->pre_proc_conference,
                'ads_post_ib' => $schedule->ads_post_ib,
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
                'bidding_number' => $schedule->bidding_number,
                'bidding_date' => $schedule->bidding_date,
                'bidding_result' => $schedule->bidding_result,
                'resolution_number_mop' => $schedule->resolution_number_mop,
            ];
        }

        // Merge PrSvp data
        foreach ($prSvps as $schedule) {
            $refId = $schedule->ref_id;
            if (!$map->has($refId)) {
                $map[$refId] = collect();
            }
            $mopUid = $schedule->mop_uid;
            $existing = $map[$refId]->get($mopUid, []);

            $map[$refId][$mopUid] = array_merge($existing, [
                'mop_uid' => $schedule->mop_uid,
                'rfq_no' => $schedule->rfq_no,
                'canvass_date' => $schedule->canvass_date,
                'date_returned_of_canvass' => $schedule->date_returned_of_canvass,
                'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date,
                'resolution_number' => $schedule->resolution_number,
            ]);
        }

        return $map;
    }
    private function mapPerItemToRow($prItem, $mopItem, array $schedule): array
    {
        return [
            'id' => $mopItem?->id,
            'prItemID' => $prItem->prItemID,
            'item_no' => $prItem->item_no,
            'description' => $prItem->description,
            'amount' => number_format((float) $prItem->amount, 2, '.', ''),
            'mode_of_procurement_id' => $mopItem?->mode_of_procurement_id,
            'uid' => $mopItem?->uid ?? 'new_' . uniqid(),
            'mode_order' => $mopItem?->mode_order ?? 1,

            // Stage and Remark
            'stage' => $prItem->prstage?->stage?->procurementstage ?? null,
            'remark' => $prItem->currentItemRemark?->remark?->remarks ?? null,

            // All schedule fields from unified map
            'ib_number' => $schedule['ib_number'] ?? null,
            'philgeps_posting_ref_no' => $schedule['philgeps_posting_ref_no'] ?? null,
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
            'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
            'list_invited_observers' => $schedule['list_invited_observers'] ?? null,
            'obsrvr_prebid_conf' => $schedule['obsrvr_prebid_conf'] ?? null,
            'obsrvr_eligibility' => $schedule['obsrvr_eligibility'] ?? null,
            'obsrvr_sub_open_of_bid' => $schedule['obsrvr_sub_open_of_bid'] ?? null,
            'obsrvr_bid' => $schedule['obsrvr_bid'] ?? null,
            'obsrvr_post_qual' => $schedule['obsrvr_post_qual'] ?? null,
            'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
            'eligibility_check' => $schedule['eligibility_check'] ?? null,
            'sub_open_bids' => $schedule['sub_open_bids'] ?? null,
            'bid_evaluation_date' => $schedule['bid_evaluation_date'] ?? null,
            'post_qualification_date' => $schedule['post_qualification_date'] ?? null,
            'bidding_number' => $schedule['bidding_number'] ?? null,
            'bidding_date' => $schedule['bidding_date'] ?? null,
            'bidding_result' => $schedule['bidding_result'] ?? null,
            'resolution_number_mop' => $schedule['resolution_number_mop'] ?? null,
            'rfq_no' => $schedule['rfq_no'] ?? null,
            'canvass_date' => $schedule['canvass_date'] ?? null,
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
            'resolution_number' => $schedule['resolution_number'] ?? null,
        ];
    }
    private function buildMopItemArray($mopLot, array $schedule): array
    {
        return [
            'id' => $mopLot->id,
            'uid' => $mopLot->uid,
            'mode_of_procurement_id' => $mopLot->mode_of_procurement_id,
            'mode_of_procurement_name' => $mopLot->modeOfProcurement?->modeofprocurements ?? 'N/A',
            'mode_order' => $mopLot->mode_order,

            // Bidding fields
            'ib_number' => $schedule['ib_number'] ?? null,
            'bidding_number' => $schedule['bidding_number'] ?? null,
            'philgeps_posting_ref_no' => $schedule['philgeps_posting_ref_no'] ?? null,
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
            'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
            'list_invited_observers' => $schedule['list_invited_observers'] ?? null,
            'obsrvr_prebid_conf' => $schedule['obsrvr_prebid_conf'] ?? null,
            'obsrvr_eligibility' => $schedule['obsrvr_eligibility'] ?? null,
            'obsrvr_sub_open_of_bid' => $schedule['obsrvr_sub_open_of_bid'] ?? null,
            'obsrvr_bid' => $schedule['obsrvr_bid'] ?? null,
            'obsrvr_post_qual' => $schedule['obsrvr_post_qual'] ?? null,
            'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
            'eligibility_check' => $schedule['eligibility_check'] ?? null,
            'sub_open_bids' => $schedule['sub_open_bids'] ?? null,
            'bid_evaluation_date' => $schedule['bid_evaluation_date'] ?? null,
            'post_qualification_date' => $schedule['post_qualification_date'] ?? null,
            'bidding_date' => $schedule['bidding_date'] ?? null,
            'bidding_result' => $schedule['bidding_result'] ?? null,
            'resolution_number_mop' => $schedule['resolution_number_mop'] ?? null,

            // SVP fields
            'rfq_no' => $schedule['rfq_no'] ?? null,
            'canvass_date' => $schedule['canvass_date'] ?? null,
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
            'resolution_number' => $schedule['resolution_number'] ?? null,
        ];
    }

    protected function loadPostProcurementData(Procurement $procurement): void
    {
        if ($this->form['procurement_type'] === 'perLot') {
            // For perLot, load single post-procurement record
            $post = PostProcurement::where('ref_id', $procurement->procID)->first();

            if ($post) {
                $this->resolutionNumber = $post->resolution_number;
                $this->bidEvaluationDate = $post->bid_evaluation_date;
                $this->postQualDate = $post->post_qual_date;
                $this->noticeOfAwardNumber = $post->notice_of_award_number;
                $this->noticeOfAward = $post->notice_of_award;
                $this->recommendingForAward = $post->recommending_for_award;
                $this->awardedAmount = $post->awarded_amount;
                $this->philgepsReferenceNo = $post->philgeps_reference_no;
                $this->awardNoticeNumber = $post->award_notice_no;
                $this->dateOfPostingOfAwardOnPhilGEPS = $post->date_of_posting_of_award_on_philgeps;
                $this->supplier_id = $post->supplier_id;
            }
        } else {
            // For perItem, load post-procurement data for each prItemID
            $this->postItems = [];

            // Get unique prItemIDs from items
            $prItemIds = collect($this->form['items'] ?? [])
                ->pluck('prItemID')
                ->filter()
                ->unique();

            // Load post-procurement records for these items
            $postRecords = PostProcurement::whereIn('ref_id', $prItemIds)->get()->keyBy('ref_id');

            foreach ($prItemIds as $prItemID) {
                $post = $postRecords->get($prItemID);

                if ($post) {
                    $this->postItems[$prItemID] = [
                        'resolutionNumber' => $post->resolution_number,
                        'bidEvaluationDate' => $post->bid_evaluation_date,
                        'postQualDate' => $post->post_qual_date,
                        'noticeOfAwardNumber' => $post->notice_of_award_number,
                        'noticeOfAward' => $post->notice_of_award,
                        'recommendingForAward' => $post->recommending_for_award,
                        'awardedAmount' => $post->awarded_amount,
                        'philgepsReferenceNo' => $post->philgeps_reference_no,
                        'awardNoticeNumber' => $post->award_notice_no,
                        'dateOfPostingOfAwardOnPhilGEPS' => $post->date_of_posting_of_award_on_philgeps,
                        'supplier_id' => $post->supplier_id,
                    ];
                }
            }
        }
    }

    protected function loadReferenceData(): void
    {
        $this->categories = Category::with(['categoryType', 'bacType'])->get();
        $this->divisions = Division::all();
        $this->clusterCommittees = ClusterCommittee::all();
        $this->venueSpecifics = VenueSpecific::all();
        $this->venueProvinces = ProvinceHuc::all();
        $this->endUsers = EndUser::all();
        $this->fundSources = FundSource::all();
        $this->modeOfProcurements = ModeOfProcurement::orderBy('id', 'asc')->get();
        $this->suppliers = Supplier::all();
    }

    public function setStep(int $step): void
    {
        $this->activeTab = $step;
    }

    public function back(): mixed
    {
        return redirect()->route('procurements.index');
    }

    public function getHasMopDataProperty(): bool
    {
        if ($this->form['procurement_type'] === 'perLot') {
            return !empty($this->form['mop_items'] ?? []);
        } else {
            // For perItem, check if any items have mode_of_procurement_id
            $items = $this->form['items'] ?? [];

            if (empty($items)) {
                return false;
            }

            // Check if at least one item has a valid mode (not mode 1 which is default)
            foreach ($items as $item) {
                $modeId = $item['mode_of_procurement_id'] ?? null;
                if ($modeId && $modeId != 1) {
                    return true;
                }
            }

            return false;
        }
    }

    public function getHasPostDataProperty(): bool
    {
        if ($this->form['procurement_type'] === 'perLot') {
            return PostProcurement::where('ref_id', $this->procurement->procID)->exists();
        } else {
            // For perItem, check if any prItemID has post-procurement data
            return !empty($this->postItems);
        }
    }
    public function toggleMopSection($index)
    {
        if (isset($this->mopToggles[$index])) {
            $this->mopToggles[$index] = !$this->mopToggles[$index];
        } else {
            $this->mopToggles[$index] = true;
        }
    }
    public function viewSupplierDetails(int $supplierId): void
    {
        $supplier = $this->suppliers->firstWhere('id', $supplierId);

        if ($supplier) {
            $this->selectedSupplier = [
                'name' => $supplier->name ?? null,
                'tin' => !empty(trim($supplier->tin)) ? trim($supplier->tin) : null,
                'address' => !empty(trim($supplier->address)) ? trim($supplier->address) : null,
                'mobile' => !empty(trim($supplier->mobile)) ? trim($supplier->mobile) : null,
                'telephone' => !empty(trim($supplier->telephone)) ? trim($supplier->telephone) : null,
                'email' => !empty(trim($supplier->email)) ? trim($supplier->email) : null,
                'contact_person' => !empty(trim($supplier->contact_person)) ? trim($supplier->contact_person) : null,
            ];
            $this->showModal = true;
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedSupplier = null;
    }
    public function updatedMopSearchTerm(): void
    {
        $this->page = 1;
    }

    public function updatedPostSearchTerm(): void
    {
        $this->postPage = 1;
    }
    public function updatedPerPage(): void
    {
        $this->page = 1;
    }

    public function updatedPostPerPage(): void
    {
        $this->postPage = 1;
    }
    public function render()
    {
        return view('livewire.procurements.procurement-view-page', [
            'suppliers' => $this->suppliers,
        ]);
    }
}
