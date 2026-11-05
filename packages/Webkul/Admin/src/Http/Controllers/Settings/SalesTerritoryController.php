<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\SalesTerritoriesDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\User\Repositories\SalesTerritoryRepository;

class SalesTerritoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected SalesTerritoryRepository $salesTerritoryRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(SalesTerritoriesDataGrid::class)->process();
        }

        return view('admin::settings.sales-territories.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'code' => 'required|unique:sales_territories,code',
            'name' => 'required',
            'status' => 'sometimes',
        ]);

        $data = request()->only(['code', 'name', 'status']);

        $data['status'] = (bool) ($data['status'] ?? false);

        Event::dispatch('settings.sales_territory.create.before');

        $salesTerritory = $this->salesTerritoryRepository->create($data);

        Event::dispatch('settings.sales_territory.create.after', $salesTerritory);

        return new JsonResponse([
            'message' => trans('admin::app.settings.sales-territories.index.create-success'),
        ]);
    }

    /**
     * Sales code details.
     *
     * @param  int  $id
     */
    public function edit($id): JsonResponse
    {
        $salesTerritory = $this->salesTerritoryRepository->findOrFail($id);

        return new JsonResponse([
            'sales_code' => $salesTerritory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(): JsonResponse
    {
        $id = request()->id;

        $this->validate(request(), [
            'code' => 'required|unique:sales_territories,code,'.$id,
            'name' => 'required',
            'status' => 'sometimes',
        ]);

        $data = request()->only(['code', 'name', 'status']);

        $data['status'] = (bool) ($data['status'] ?? false);

        Event::dispatch('settings.sales_territory.update.before', $id);

        $salesTerritory = $this->salesTerritoryRepository->update($data, $id);

        Event::dispatch('settings.sales_territory.update.after', $salesTerritory);

        return new JsonResponse([
            'message' => trans('admin::app.settings.sales-territories.index.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id): JsonResponse
    {
        $salesTerritory = $this->salesTerritoryRepository->findOrFail($id);

        if ($salesTerritory->admins()->count()) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.sales-territories.index.being-used'),
            ], 400);
        }

        try {
            Event::dispatch('settings.sales_territory.delete.before', $id);

            $this->salesTerritoryRepository->delete($id);

            Event::dispatch('settings.sales_territory.delete.after', $id);

            return new JsonResponse([
                'message' => trans('admin::app.settings.sales-territories.index.delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.sales-territories.index.delete-failed'),
            ], 500);
        }
    }
}
