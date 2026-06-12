<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistrictRequest;
use App\Http\Requests\UpdateDistrictRequest;
use App\Http\Resources\DistrictResource;
use App\Models\District;
use App\Services\GeoCacheService;
use Inertia\Inertia;
use Inertia\Response;

class DistrictController extends Controller
{
    public function index(): Response
    {
        $search = request('search');
        $regencyId = request('regency_id');

        $districts = DistrictResource::collection(
            District::query()
                ->with('regency.province')
                ->with('creator')
                ->when($search, fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->when($regencyId, fn ($query, $regencyId) => $query
                    ->where('regency_id', $regencyId))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        );

        return Inertia::render('Districts/Index', [
            'districts' => $districts,
            'filters' => request()->only('search', 'regency_id'),
            'regencies' => GeoCacheService::getRegencies(),
            'stats' => [
                'total' => ['label' => 'Total Districts', 'value' => District::count(), 'trend' => 0],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Districts/Create', [
            'regencies' => GeoCacheService::getRegencies(),
        ]);
    }

    public function store(StoreDistrictRequest $request)
    {
        District::create([...$request->validated(), 'created_by' => auth()->id()]);

        return to_route('districts.index')
            ->with('success', 'District created successfully.');
    }

    public function edit(District $district): Response
    {
        $district->load('regency');

        return Inertia::render('Districts/Edit', [
            'district' => [
                'id' => $district->id,
                'name' => $district->name,
                'regency_id' => $district->regency_id,
                'regency' => $district->regency,
            ],
            'regencies' => GeoCacheService::getRegencies(),
        ]);
    }

    public function update(UpdateDistrictRequest $request, District $district)
    {
        $district->update($request->validated());

        return to_route('districts.index')
            ->with('success', 'District updated successfully.');
    }

    public function destroy(District $district)
    {
        $district->delete();

        return to_route('districts.index')
            ->with('success', 'District deleted successfully.');
    }
}
