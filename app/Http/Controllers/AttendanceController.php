<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function createAttendance(Request $req)
    {
        if (!request()->bearerToken()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is required.'
            ], 401);
        }

        $validator = Validator::make($req->all(), [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
            'status' => 'required|in:hadir,izin,sakit,alpa',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $attendance = Attendance::create([
            'user_id' => $req->user_id,
            'date' => $req->date,
            'time' => $req->time,
            'status' => $req->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Presensi berhasil dicatat',
            'data' => [
                'attendance_id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'date' => $attendance->date,
                'time' => $attendance->time,
                'status' => $attendance->status,
            ],
        ], 201);
    }

    public function getAttendanceHistory($user_id)
    {

        $attendanceHistory = Attendance::where('user_id', $user_id)->get();

        if ($attendanceHistory->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada riwayat presensi untuk pengguna ini',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $attendanceHistory,
        ]);
    }

    public function getMonthlySummary($user_id, Request $request)
    {
        if (!request()->bearerToken()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is required.'
            ], 401);
        }

        $month = $request->query('month', Carbon::now()->format('Y-m'));

        try {
            $carbonMonth = Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format bulan tidak valid. Gunakan YYYY-MM.',
            ], 400);
        }

        $attendances = Attendance::where('user_id', $user_id)
                             ->whereMonth('date', $carbonMonth->month)
                             ->whereYear('date', $carbonMonth->year)
                             ->get();

        if ($attendances->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user_id' => $user_id,
                    'month' => $month,
                    'attendance_summary' => [
                        'hadir' => 0,
                        'izin' => 0,
                        'sakit' => 0,
                        'alpa' => 0,
                    ],
                ],
            ]);
        }

        $summary = [
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'alpa' => $attendances->where('status', 'alpa')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'user_id' => $user_id,
                'month' => $month,
                'attendance_summary' => $summary,
            ],
        ]);
    }

    public function analysis(Request $request)
    {
        if (!request()->bearerToken()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is required.'
            ], 401);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'required|in:siswa,karyawan',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $users = User::where('role', $request->group_by)->get();

        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])
            ->whereIn('user_id', $users->pluck('id'))
            ->with('user')
            ->get();

        $totalUsers = $users->count();
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalAlpa = $attendances->where('status', 'alpa')->count();

        $totalAttendance = $totalHadir + $totalIzin + $totalSakit + $totalAlpa;

        $attendanceRate = [
            'hadir_percentage' => $totalAttendance > 0 ? round(($totalHadir / $totalAttendance) * 100, 2) : 0,
            'izin_percentage' => $totalAttendance > 0 ? round(($totalIzin / $totalAttendance) * 100, 2) : 0,
            'sakit_percentage' => $totalAttendance > 0 ? round(($totalSakit / $totalAttendance) * 100, 2) : 0,
            'alpa_percentage' => $totalAttendance > 0 ? round(($totalAlpa / $totalAttendance) * 100, 2) : 0,
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'analysis_period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'grouped_analysis' => [
                    'group' => $request->group_by,
                    'total_users' => $totalUsers,
                    'attendance_rate' => $attendanceRate,
                    'total_attendance' => [
                        'hadir' => $totalHadir,
                        'izin' => $totalIzin,
                        'sakit' => $totalSakit,
                        'alpa' => $totalAlpa,
                    ],
                ],
            ]
        ]);
    }
}
