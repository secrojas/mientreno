<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Vista principal de reportes (redirect a weekly)
     */
    public function index()
    {
        return redirect()->route('reports.weekly');
    }

    /**
     * Reporte semanal
     */
    public function weekly(?int $year = null, ?int $week = null)
    {
        $user = Auth::user();
        $report = $this->reportService->getWeeklyReport($user, $year, $week);

        return view('reports.weekly', compact('report'));
    }

    /**
     * Reporte mensual
     */
    public function monthly(?int $year = null, ?int $month = null)
    {
        $user = Auth::user();
        $report = $this->reportService->getMonthlyReport($user, $year, $month);

        return view('reports.monthly', compact('report'));
    }

    /**
     * Exportar reporte semanal a PDF
     */
    public function exportWeeklyPDF(int $year, int $week)
    {
        $user = Auth::user();
        $report = $this->reportService->getWeeklyReport($user, $year, $week);

        $pdf = Pdf::loadView('reports.pdf.weekly', compact('report'));

        // Configuración del PDF
        $pdf->setPaper('a4', 'portrait');

        $filename = "reporte-semanal-{$year}-semana-{$week}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Exportar reporte mensual a PDF
     */
    public function exportMonthlyPDF(int $year, int $month)
    {
        $user = Auth::user();
        $report = $this->reportService->getMonthlyReport($user, $year, $month);

        $pdf = Pdf::loadView('reports.pdf.monthly', compact('report'));

        // Configuración del PDF
        $pdf->setPaper('a4', 'portrait');

        $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->locale('es')->monthName;
        $filename = "reporte-mensual-{$monthName}-{$year}.pdf";

        return $pdf->download($filename);
    }
}
