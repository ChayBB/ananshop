<?php

namespace Webkul\Admin\Http\Controllers\Reporting;

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
        'stock-control-center'     => 'getStockControlCenterStats',
    ];

    /**
     * Display the stock control center.
     *
     * @return View
     */
    public function index()
    {
        return view('admin::reporting.stock.index');
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
