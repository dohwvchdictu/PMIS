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
    public bool $showTable = false;
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

    // Post procurement data
    public ?string $resolutionNumber = null;
    public ?string $bidEvaluationDate = null;
    public ?string $postQualDate = null;
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
            'pr_items',
            'category.categoryType',
            'category.bacType',
            'mopLots.modeOfProcurement',
            'bacApprovedPr'
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

    private function buildScheduleMap(Collection $bidSchedules, Collection $prSvps): Collection
    {
        $map = collect();

        foreach ($bidSchedules as $uid => $schedule) {
            $map[$uid] = [
                'ib_number' => $schedule->ib_number,
                'pre_proc_conference' => $schedule->pre_proc_conference,
                'ads_post_ib' => $schedule->ads_post_ib,
                'pre_bid_conf' => $schedule->pre_bid_conf,
                'eligibility_check' => $schedule->eligibility_check,
                'sub_open_bids' => $schedule->sub_open_bids,
                'bidding_number' => $schedule->bidding_number,
                'bidding_date' => $schedule->bidding_date,
                'bidding_result' => $schedule->bidding_result,
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
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
            'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
            'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
            'eligibility_check' => $schedule['eligibility_check'] ?? null,
            'sub_open_bids' => $schedule['sub_open_bids'] ?? null,
            'bidding_date' => $schedule['bidding_date'] ?? null,
            'bidding_result' => $schedule['bidding_result'] ?? null,

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
        $post = PostProcurement::where('ref_id', $procurement->procID)->first();

        if ($post) {
            $this->resolutionNumber = $post->resolution_number;
            $this->bidEvaluationDate = $post->bid_evaluation_date;
            $this->postQualDate = $post->post_qual_date;
            $this->noticeOfAward = $post->notice_of_award;
            $this->recommendingForAward = $post->recommending_for_award;
            $this->awardedAmount = $post->awarded_amount;
            $this->philgepsReferenceNo = $post->philgeps_reference_no;
            $this->awardNoticeNumber = $post->award_notice_no;
            $this->dateOfPostingOfAwardOnPhilGEPS = $post->date_of_posting_of_award_on_philgeps;
            $this->supplier_id = $post->supplier_id;
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
        return !empty($this->form['mop_items'] ?? []);
    }

    public function getHasPostDataProperty(): bool
    {
        return PostProcurement::where('ref_id', $this->procurement->procID)->exists();
    }

    public function render()
    {
        return view('livewire.procurements.procurement-view-page');
    }
}
