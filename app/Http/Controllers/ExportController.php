<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Occupation;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    protected array $allowed = [
        'users',
        'roles',
        'provinces',
        'regencies',
        'districts',
        'villages',
        'occupations',
    ];

    public function export(string $resource): StreamedResponse
    {
        abort_unless(in_array($resource, $this->allowed), 404);

        if (! auth()->user()->hasPermission($resource, 'read')) {
            abort(403, 'You do not have permission to export this resource.');
        }

        $method = "export_{$resource}";

        return $this->$method();
    }

    protected function csvHeaders(string $filename): array
    {
        return [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];
    }

    protected function export_users(): StreamedResponse
    {
        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Roles', 'Created At']);

            $search = request('search');

            User::query()
                ->when(! auth()->user()->hasPermission('users', 'read'), fn ($query) => $query
                    ->where(function ($q) {
                        $q->where('created_by', auth()->id())
                            ->orWhere('id', auth()->id());
                    }))
                ->when($search, fn ($query, $search) => $query
                    ->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%'])
                            ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($search).'%']);
                    }))
                ->with('roles')
                ->orderBy('created_at', 'desc')
                ->each(function ($user) use ($handle) {
                    fputcsv($handle, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->roles->pluck('name')->implode(', '),
                        $user->created_at,
                    ]);
                });

            fclose($handle);
        }, 200, $this->csvHeaders('users.csv'));
    }

    protected function export_roles(): StreamedResponse
    {
        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Slug', 'Description', 'Permissions', 'Users Count', 'Created At']);

            Role::query()
                ->when(! auth()->user()->hasPermission('roles', 'read'), fn ($query) => $query
                    ->where('created_by', auth()->id()))
                ->when(request('search'), fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->withCount('users')
                ->orderBy('created_at', 'desc')
                ->each(function ($role) use ($handle) {
                    fputcsv($handle, [
                        $role->id,
                        $role->name,
                        $role->slug,
                        $role->description,
                        is_array($role->permissions) ? json_encode($role->permissions) : $role->permissions,
                        $role->users_count,
                        $role->created_at,
                    ]);
                });

            fclose($handle);
        }, 200, $this->csvHeaders('roles.csv'));
    }

    protected function export_provinces(): StreamedResponse
    {
        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Code', 'Created At']);

            $search = request('search');

            Province::query()
                ->when($search, fn ($query, $search) => $query
                    ->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%'])
                            ->orWhereRaw('LOWER(code) LIKE ?', ['%'.strtolower($search).'%']);
                    }))
                ->orderBy('created_at', 'desc')
                ->each(function ($province) use ($handle) {
                    fputcsv($handle, [
                        $province->id,
                        $province->name,
                        $province->code,
                        $province->created_at,
                    ]);
                });

            fclose($handle);
        }, 200, $this->csvHeaders('provinces.csv'));
    }

    protected function export_regencies(): StreamedResponse
    {
        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Province Name', 'Created At']);

            $search = request('search');
            $provinceId = request('province_id');

            Regency::query()
                ->with('province')
                ->when($search, fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->when($provinceId, fn ($query, $provinceId) => $query
                    ->where('province_id', $provinceId))
                ->orderBy('created_at', 'desc')
                ->each(function ($regency) use ($handle) {
                    fputcsv($handle, [
                        $regency->id,
                        $regency->name,
                        $regency->province->name ?? '',
                        $regency->created_at,
                    ]);
                });

            fclose($handle);
        }, 200, $this->csvHeaders('regencies.csv'));
    }

    protected function export_districts(): StreamedResponse
    {
        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Regency Name', 'Province Name', 'Created At']);

            $search = request('search');
            $regencyId = request('regency_id');

            District::query()
                ->with('regency.province')
                ->when($search, fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->when($regencyId, fn ($query, $regencyId) => $query
                    ->where('regency_id', $regencyId))
                ->orderBy('created_at', 'desc')
                ->each(function ($district) use ($handle) {
                    fputcsv($handle, [
                        $district->id,
                        $district->name,
                        $district->regency->name ?? '',
                        $district->regency->province->name ?? '',
                        $district->created_at,
                    ]);
                });

            fclose($handle);
        }, 200, $this->csvHeaders('districts.csv'));
    }

    protected function export_villages(): StreamedResponse
    {
        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'District Name', 'Regency Name', 'Province Name', 'Postal Code', 'Created At']);

            $search = request('search');
            $districtId = request('district_id');

            Village::query()
                ->with('district.regency.province')
                ->when($search, fn ($query, $search) => $query
                    ->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%'])
                            ->orWhereRaw('LOWER(postal_code) LIKE ?', ['%'.strtolower($search).'%']);
                    }))
                ->when($districtId, fn ($query, $districtId) => $query
                    ->where('district_id', $districtId))
                ->orderBy('created_at', 'desc')
                ->each(function ($village) use ($handle) {
                    fputcsv($handle, [
                        $village->id,
                        $village->name,
                        $village->district->name ?? '',
                        $village->district->regency->name ?? '',
                        $village->district->regency->province->name ?? '',
                        $village->postal_code,
                        $village->created_at,
                    ]);
                });

            fclose($handle);
        }, 200, $this->csvHeaders('villages.csv'));
    }

    protected function export_occupations(): StreamedResponse
    {
        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Created At']);

            Occupation::query()
                ->when(request('search'), fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->orderBy('created_at', 'desc')
                ->each(function ($occupation) use ($handle) {
                    fputcsv($handle, [
                        $occupation->id,
                        $occupation->name,
                        $occupation->created_at,
                    ]);
                });

            fclose($handle);
        }, 200, $this->csvHeaders('occupations.csv'));
    }
}
