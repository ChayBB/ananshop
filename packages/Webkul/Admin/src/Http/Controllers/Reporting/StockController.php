<?php

namespace Webkul\Admin\Http\Controllers\Reporting;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Request param functions.
     *
     * @var array
     */
    protected $typeFunctions = [
        'stock-threshold-products' => 'getStockThresholdProductsStats',
    ];

    /**
     * Stock reporting has a single report, so send the index straight to it
     * rather than showing a landing page with one widget on it.
     *
     * @return RedirectResponse
     */
    public function index()
    {
        return redirect()->route('admin.reporting.stock.view', [
            'type' => 'stock-threshold-products',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function view()
    {
        if ($this->validateRequestedType()) {
            abort(404);
        }

        return view('admin::reporting.view')->with([
            'entity'    => 'stock',
            'startDate' => $this->reportingHelper->getStartDate(),
            'endDate'   => $this->reportingHelper->getEndDate(),
        ]);
    }
}
