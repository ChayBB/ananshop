<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\SalesCodesDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\User\Repositories\SalesCodeRepository;

class SalesCodeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected SalesCodeRepository $salesCodeRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(SalesCodesDataGrid::class)->process();
        }

        return view('admin::settings.sales-codes.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'code' => 'required|unique:sales_codes,code',
            'name' => 'nullable',
            'status' => 'sometimes',
        ]);

        $data = request()->only(['code', 'name', 'status']);

        $data['status'] = (bool) ($data['status'] ?? false);

        Event::dispatch('settings.sales_code.create.before');

        $salesCode = $this->salesCodeRepository->create($data);

        Event::dispatch('settings.sales_code.create.after', $salesCode);

        return new JsonResponse([
            'message' => trans('admin::app.settings.sales-codes.index.create-success'),
        ]);
    }

    /**
     * Sales code details.
     *
     * @param  int  $id
     */
    public function edit($id): JsonResponse
    {
        $salesCode = $this->salesCodeRepository->findOrFail($id);

        return new JsonResponse([
            'sales_code' => $salesCode,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(): JsonResponse
    {
        $id = request()->id;

        $this->validate(request(), [
            'code' => 'required|unique:sales_codes,code,'.$id,
            'name' => 'nullable',
            'status' => 'sometimes',
        ]);

        $data = request()->only(['code', 'name', 'status']);

        $data['status'] = (bool) ($data['status'] ?? false);

        Event::dispatch('settings.sales_code.update.before', $id);

        $salesCode = $this->salesCodeRepository->update($data, $id);

        Event::dispatch('settings.sales_code.update.after', $salesCode);

        return new JsonResponse([
            'message' => trans('admin::app.settings.sales-codes.index.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id): JsonResponse
    {
        $salesCode = $this->salesCodeRepository->findOrFail($id);

        if ($salesCode->admins()->count()) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.sales-codes.index.being-used'),
            ], 400);
        }

        try {
            Event::dispatch('settings.sales_code.delete.before', $id);

            $this->salesCodeRepository->delete($id);

            Event::dispatch('settings.sales_code.delete.after', $id);

            return new JsonResponse([
                'message' => trans('admin::app.settings.sales-codes.index.delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.sales-codes.index.delete-failed'),
            ], 500);
        }
    }
}
