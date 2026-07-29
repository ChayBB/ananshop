<?php

namespace Webkul\Admin\Http\Controllers\Catalog;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Catalog\ProductBatchDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Inventory\Repositories\InventorySourceRepository;
use Webkul\Product\Models\ProductBatchProxy;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Services\StockService;

class ProductBatchController extends Controller
{
    /**
     * Create a controller instance.
     */
    public function __construct(
        protected StockService $stockService,
        protected ProductRepository $productRepository,
        protected InventorySourceRepository $inventorySourceRepository
    ) {}

    /**
     * Display a listing of the batches.
     *
     * @return View|JsonResponse
     */
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(ProductBatchDataGrid::class)->process();
        }

        return view('admin::catalog.batches.index');
    }

    /**
     * Show the receiving form.
     *
     * @return View
     */
    public function create()
    {
        /**
         * Batches only make sense for stock that is actually counted, so
         * configurable parents and other non-stocked types are left out.
         */
        $products = $this->productRepository
            ->where('type', 'simple')
            ->orderBy('sku')
            ->get();

        $inventorySources = $this->inventorySourceRepository->findWhere(['status' => 1]);

        return view('admin::catalog.batches.create', compact('products', 'inventorySources'));
    }

    /**
     * Receive a batch into stock.
     *
     * @return RedirectResponse
     */
    public function store()
    {
        $this->validate(request(), [
            'product_id'          => 'required|integer|exists:products,id',
            'inventory_source_id' => 'required|integer|exists:inventory_sources,id',
            'qty'                 => 'required|integer|min:1',
            'batch_number'        => 'nullable|string|max:255',
            'received_at'         => 'required|date',
            'expired_at'          => 'nullable|date|after_or_equal:received_at',
            'unit_cost'           => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        $this->stockService->receive(request()->only([
            'product_id',
            'inventory_source_id',
            'qty',
            'batch_number',
            'received_at',
            'expired_at',
            'unit_cost',
            'notes',
        ]));

        session()->flash('success', trans('admin::app.catalog.batches.create.received-success'));

        return redirect()->route('admin.catalog.batches.index');
    }

    /**
     * Remove a batch, returning its remaining stock to the source.
     *
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        $batch = ProductBatchProxy::modelClass()::findOrFail($id);

        $this->stockService->discardBatch($batch);

        return new JsonResponse([
            'message' => trans('admin::app.catalog.batches.index.delete-success'),
        ]);
    }
}
