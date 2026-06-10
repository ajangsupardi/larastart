<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVillageRequest;
use App\Http\Requests\UpdateVillageRequest;
use App\Http\Resources\VillageResource;
use App\Models\District;
use App\Models\Village;
use Inertia\Inertia;
use Inertia\Response;

class VillageController extends Controller
{
    public function index(): Response
    {
        $search = request('search');
        $districtId = request('district_id');

        $villages = VillageResource::collection(
            Village::query()
                ->with('district.regency.province')
                ->when($search, fn ($query, $search) => $query
                    ->where('name', 'like', '%'.$search.'%'))
                ->when($districtId, fn ($query, $districtId) => $query
                    ->where('district_id', $districtId))
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString()
        );

        return Inertia::render('Villages/Index', [
            'villages' => $villages,
            'filters' => request()->only('search', 'district_id'),
            'districts' => District::orderBy('name')->get(['id', 'name']),
            'stats' => [
                'total' => ['label' => 'Total Villages', 'value' => Village::count(), 'trend' => 0],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Villages/Create', [
            'districts' => District::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreVillageRequest $request)
    {
        Village::create([...$request->validated(), 'created_by' => auth()->id()]);

        return to_route('villages.index')
            ->with('success', 'Village created successfully.');
    }

    public function edit(Village $village): Response
    {
        $village->load('district');

        return Inertia::render('Villages/Edit', [
            'village' => [
                'id' => $village->id,
                'name' => $village->name,
                'postal_code' => $village->postal_code,
                'district_id' => $village->district_id,
                'district' => $village->district,
            ],
            'districts' => District::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateVillageRequest $request, Village $village)
    {
        $village->update($request->validated());

        return to_route('villages.index')
            ->with('success', 'Village updated successfully.');
    }

    public function destroy(Village $village)
    {
        $village->delete();

        return to_route('villages.index')
            ->with('success', 'Village deleted successfully.');
    }
}
