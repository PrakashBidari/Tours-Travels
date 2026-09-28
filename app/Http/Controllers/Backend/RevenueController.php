<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RevenueController extends Controller
{
    public function index(Request $request): View
    {
        return view('backend.revenue.index', $this->reportData($request));
    }

    public function pdf(Request $request): Response
    {
        $data = $this->reportData($request);

        return Pdf::loadView('backend.revenue.pdf', $data)
            ->download('revenue-report-'.now()->format('Y-m-d').'.pdf');
    }

    public function doc(Request $request): BinaryFileResponse
    {
        $data = $this->reportData($request);

        $phpWord = new PhpWord;
        $section = $phpWord->addSection();

        $section->addTitle('Revenue Report', 1);
        $section->addText("Period: {$data['from']->format('M d, Y')} — {$data['to']->format('M d, Y')}");
        $section->addTextBreak();

        $section->addText("Total bookings: {$data['totals']['bookings_count']}");
        $section->addText('Total platform revenue: '.number_format((float) $data['totals']['admin_revenue'], 2));
        $section->addText('Total vendor payouts: '.number_format((float) $data['totals']['vendor_payout'], 2));
        $section->addTextBreak();

        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $table->addRow();
        foreach (['Vendor', 'Bookings', 'Platform Revenue', 'Vendor Payout'] as $header) {
            $table->addCell(2500)->addText($header, ['bold' => true]);
        }

        foreach ($data['revenueByVendor'] as $row) {
            $table->addRow();
            $table->addCell(2500)->addText($row->vendor?->name ?? 'Unassigned');
            $table->addCell(2500)->addText((string) $row->bookings_count);
            $table->addCell(2500)->addText(number_format((float) $row->admin_revenue, 2));
            $table->addCell(2500)->addText(number_format((float) $row->vendor_payout, 2));
        }

        $path = storage_path('app/revenue-report-'.now()->timestamp.'.docx');
        $phpWord->save($path, 'Word2007');

        return response()->download($path)->deleteFileAfterSend(true);
    }

    protected function reportData(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->string('from')) : now()->subDays(30);
        $to = $request->filled('to') ? Carbon::parse($request->string('to')) : now();

        $base = Booking::whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->whereIn('status', ['confirmed', 'completed']);

        $revenueByVendor = (clone $base)->with('vendor')
            ->selectRaw('vendor_id, SUM(commission_amount) as admin_revenue, SUM(vendor_payout_amount) as vendor_payout, SUM(total_price) as gross, COUNT(*) as bookings_count')
            ->groupBy('vendor_id')
            ->orderByDesc('admin_revenue')
            ->get();

        $totals = [
            'bookings_count' => (clone $base)->count(),
            'admin_revenue' => (clone $base)->sum('commission_amount'),
            'vendor_payout' => (clone $base)->sum('vendor_payout_amount'),
            'gross' => (clone $base)->sum('total_price'),
        ];

        return compact('from', 'to', 'revenueByVendor', 'totals');
    }
}
