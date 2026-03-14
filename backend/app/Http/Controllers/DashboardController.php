<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DashboardController extends Controller
{
    public function index()
    {
        // Guard: redirect to login if not authenticated
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('user_id'));
        if (!$user) {
            return redirect()->route('login');
        }
        if (!$user) {
            return redirect()->route('login');
        }
        return view('dashboard', [
            'user' => $user,
            'currentDate' => now()->format('l, F j, Y'),
        ]);
    }

    public function scheduleList()
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        return view('schedule-list', [
            'user_name'   => session('user_name', 'User'),
            'currentDate' => now()->format('l, F j, Y'),
        ]);
    }

    public function exportSummary(Request $request)
    {
        if (!session('user_id')) {
            abort(403, 'Unauthorized');
        }

        try {
            $month = $request->input('month', 'all'); // 'all' or 0-11 (Jan-Dec)
            $year = $request->input('year', date('Y'));

            // Get all schedules from database directly (regardless of status)
            $allSchedules = Event::orderBy('date', 'asc')->get()->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->date,
                    'end_date' => $event->end_date,
                    'location' => $event->location,
                    'classification' => $event->classification,
                    'description' => $event->description,
                    'status' => $event->status,
                    'color' => $event->color,
                    'day_overrides' => $event->day_overrides,
                ];
            })->toArray();

            // Filter by month/year - include ALL statuses (upcoming, completed, cancelled)
            $filteredSchedules = array_filter($allSchedules, function ($schedule) use ($month, $year) {
                $scheduleDate = new \DateTime($schedule['date']);
                
                if ($month === 'all') {
                    return $scheduleDate->format('Y') == $year;
                } else {
                    return $scheduleDate->format('n') == ($month + 1) && $scheduleDate->format('Y') == $year;
                }
            });

            // Log for debugging
            \Log::info('Export Summary - Month: ' . $month . ', Year: ' . $year . ', Found: ' . count($filteredSchedules) . ' schedules');

            // Create Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
            $reportTitle = $month === 'all' ? "Year $year" : "$monthNames[$month] $year";
        
            
            $sheet->setCellValue('A1', "Schedule Summary - $reportTitle");
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
            // Column headers
            $sheet->fromArray([
                ['Date', 'Title', 'Location', 'Classification', 'Status', 'Details'],
            ], null, 'A3');
    
            // Style header row
            $headerRange = 'A3:F3';
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4F46E5');
            $sheet->getStyle($headerRange)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($headerRange)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
    
            // Add data rows
            $row = 4;
            foreach ($filteredSchedules as $schedule) {
                $sheet->fromArray([[
                    date('F j, Y', strtotime($schedule['date'])),
                    $schedule['title'],
                    $schedule['location'] ?? '',
                    $schedule['classification'] ?? '',
                    ucfirst($schedule['status']),
                    $schedule['description'] ?? '',
                ]], null, 'A' . $row);
    
                // Style data row
                $range = "A{$row}:F{$row}";
                $sheet->getStyle($range)->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                $sheet->getStyle($range)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
    
                $row++;
            }
    
            // Auto-size columns
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
    
            // Download
            $filename = str_replace(' ', '_', strtolower($reportTitle)) . '_summary.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
    
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            \Log::error('Export Summary Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating summary: ' . $e->getMessage()
            ], 500);
        }
    }
}
