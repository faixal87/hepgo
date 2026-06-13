<?php

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Enums\RecordStatus;
use App\Models\Area;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Url(as: 'keyword', except: '')]
    public string $keyword = '';

    #[Url(as: 'area', except: '')]
    public string $area = '';

    #[Url(as: 'category', except: '')]
    public string $category = '';

    #[Url(as: 'min', except: '')]
    public string $priceMin = '';

    #[Url(as: 'max', except: '')]
    public string $priceMax = '';

    #[Url(as: 'status', except: '')]
    public string $status = '';

    #[Url(as: 'gender', except: '')]
    public string $gender = '';

    #[Url(as: 'facilities', except: [])]
    public array $facilities = [];

    #[Url(as: 'sort', except: 'terbaru')]
    public string $sort = 'terbaru';

    public function updated(string $name, mixed $value = null): void
    {
        if ($name !== 'page') {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->keyword = '';
        $this->area = '';
        $this->category = '';
        $this->priceMin = '';
        $this->priceMax = '';
        $this->status = '';
        $this->gender = '';
        $this->facilities = [];
        $this->sort = 'terbaru';

        $this->resetPage();
    }

    public function render()
    {
        return $this->view([
            'areas' => Area::query()
                ->where('status', RecordStatus::ACTIVE->value)
                ->orderBy('name')
                ->get(),
            'categories' => Category::query()
                ->where('status', RecordStatus::ACTIVE->value)
                ->orderBy('name')
                ->get(),
            'facilityOptions' => Facility::query()
                ->orderBy('name')
                ->get(),
            'genderOptions' => GenderPreference::options(),
            'properties' => $this->propertiesQuery()->paginate(8),
            'sortOptions' => [
                'terbaru' => 'Terbaru',
                'price_asc' => 'Harga rendah ke tinggi',
                'price_desc' => 'Harga tinggi ke rendah',
                'distance' => 'Jarak terdekat',
            ],
            'statusOptions' => [
                PropertyAvailabilityStatus::AVAILABLE->value => PropertyAvailabilityStatus::AVAILABLE->label(),
                PropertyAvailabilityStatus::FULL->value => PropertyAvailabilityStatus::FULL->label(),
            ],
        ]);
    }

    private function propertiesQuery(): Builder
    {
        $query = Property::query()
            ->publiclyVisible()
            ->with(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage']);

        if (filled($this->keyword)) {
            $keyword = '%'.trim($this->keyword).'%';

            $query->where(function (Builder $query) use ($keyword): void {
                $query
                    ->where('title', 'like', $keyword)
                    ->orWhere('address', 'like', $keyword)
                    ->orWhere('description', 'like', $keyword)
                    ->orWhereHas('area', fn (Builder $areaQuery) => $areaQuery->where('name', 'like', $keyword));
            });
        }

        if (filled($this->area)) {
            $query->where('area_id', $this->area);
        }

        if (filled($this->category)) {
            $query->where('category_id', $this->category);
        }

        if (is_numeric($this->priceMin)) {
            $query->where('price', '>=', (float) $this->priceMin);
        }

        if (is_numeric($this->priceMax)) {
            $query->where('price', '<=', (float) $this->priceMax);
        }

        if (in_array($this->status, [
            PropertyAvailabilityStatus::AVAILABLE->value,
            PropertyAvailabilityStatus::FULL->value,
        ], true)) {
            $query->where('status', $this->status);
        }

        if (in_array($this->gender, GenderPreference::values(), true)) {
            $query->where('gender_preference', $this->gender);
        }

        $facilityIds = collect($this->facilities)
            ->filter()
            ->map(fn ($facilityId) => (int) $facilityId)
            ->filter()
            ->values()
            ->all();

        if ($facilityIds !== []) {
            $query->whereHas('facilities', fn (Builder $facilityQuery) => $facilityQuery->whereIn('facilities.id', $facilityIds));
        }

        match ($this->sort) {
            'price_asc' => $query->orderBy('price')->latest('id'),
            'price_desc' => $query->orderByDesc('price')->latest('id'),
            'distance' => $query->orderByRaw('distance_km IS NULL')->orderBy('distance_km')->latest('id'),
            default => $query->latest(),
        };

        return $query;
    }
};
?>

<div class="grid gap-6 lg:grid-cols-[320px_1fr]">
    <aside class="h-fit rounded-3xl bg-white p-4 shadow-sm ring-1 ring-zinc-200 lg:sticky lg:top-24">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-extrabold text-zinc-950">Tapisan Carian</h2>
                <p class="mt-1 text-sm text-zinc-500">Keputusan dikemaskini secara automatik.</p>
            </div>
            <button type="button" wire:click="clearFilters" class="rounded-full bg-zinc-100 px-3 py-2 text-xs font-bold text-zinc-700 transition hover:bg-zinc-200">
                Kosongkan
            </button>
        </div>

        <div class="mt-5 space-y-4">
            <div>
                <label for="keyword" class="text-sm font-bold text-zinc-700">Kata Kunci</label>
                <input
                    id="keyword"
                    type="search"
                    wire:model.live.debounce.500ms="keyword"
                    placeholder="Tajuk, kawasan atau alamat"
                    class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                <div>
                    <label for="area" class="text-sm font-bold text-zinc-700">Kawasan</label>
                    <select id="area" wire:model.live="area" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua kawasan</option>
                        @foreach ($areas as $areaOption)
                            <option value="{{ $areaOption->id }}">{{ $areaOption->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="category" class="text-sm font-bold text-zinc-700">Kategori</label>
                    <select id="category" wire:model.live="category" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $categoryOption)
                            <option value="{{ $categoryOption->id }}">{{ $categoryOption->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="priceMin" class="text-sm font-bold text-zinc-700">Harga Minimum</label>
                    <input id="priceMin" type="number" min="0" step="50" wire:model.live.debounce.500ms="priceMin" placeholder="RM" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="priceMax" class="text-sm font-bold text-zinc-700">Harga Maksimum</label>
                    <input id="priceMax" type="number" min="0" step="50" wire:model.live.debounce.500ms="priceMax" placeholder="RM" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                <div>
                    <label for="status" class="text-sm font-bold text-zinc-700">Status Kekosongan</label>
                    <select id="status" wire:model.live="status" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua status</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="gender" class="text-sm font-bold text-zinc-700">Keutamaan Penyewa</label>
                    <select id="gender" wire:model.live="gender" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua keutamaan</option>
                        @foreach ($genderOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="sort" class="text-sm font-bold text-zinc-700">Susunan</label>
                <select id="sort" wire:model.live="sort" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @foreach ($sortOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <p class="text-sm font-bold text-zinc-700">Kemudahan</p>
                <div class="mt-2 grid gap-2">
                    @foreach ($facilityOptions as $facility)
                        <label class="flex items-center gap-3 rounded-2xl bg-zinc-50 px-3 py-2 text-sm font-semibold text-zinc-700 ring-1 ring-zinc-100">
                            <input type="checkbox" value="{{ $facility->id }}" wire:model.live="facilities" class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500">
                            <span>{{ $facility->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </aside>

    <div class="space-y-4">
        <div class="flex flex-col gap-3 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-zinc-200 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-zinc-500">Jumlah ditemui</p>
                <p class="text-2xl font-extrabold text-zinc-950">{{ number_format($properties->total()) }} rumah sewa</p>
            </div>
            <div wire:loading.delay class="rounded-full bg-amber-100 px-4 py-2 text-sm font-bold text-amber-900 ring-1 ring-amber-200">
                Sedang memuatkan...
            </div>
        </div>

        @if ($properties->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($properties as $property)
                    @include('public.properties._card', ['property' => $property])
                @endforeach
            </div>

            <div class="rounded-3xl bg-white p-4 shadow-sm ring-1 ring-zinc-200">
                {{ $properties->links() }}
            </div>
        @else
            <div class="rounded-3xl bg-white p-10 text-center shadow-sm ring-1 ring-zinc-200">
                <p class="text-lg font-extrabold text-zinc-950">Tiada rekod ditemui berdasarkan carian anda. Sila ubah tapisan carian.</p>
            </div>
        @endif
    </div>
</div>
