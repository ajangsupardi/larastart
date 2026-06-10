<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistrictRequest;
use App\Http\Requests\UpdateDistrictRequest;
use App\Http\Resources\DistrictResource;
use App\Models\District;
use App\Models\Regency;
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
                ->when($search, fn ($query, $search) => $query
                    ->where('name', 'like', '%'.$search.'%'))
                ->when($regencyId, fn ($query, $regencyId) => $query
                    ->where('regency_id', $regencyId))
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString()
        );

        return Inertia::render('Districts/Index', [
            'districts' => $districts,
            'filters' => request()->only('search', 'regency_id'),
            'regencies' => Regency::orderBy('name')->get(['id', 'name']),
            'stats' => [
                'total' => ['label' => 'Total Districts', 'value' => District::count(), 'trend' => 0],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Districts/Create', [
            'regencies' => Regency::orderBy('name')->get(['id', 'name']),
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
            'regencies' => Regency::orderBy('name')->get(['id', 'name']),
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
