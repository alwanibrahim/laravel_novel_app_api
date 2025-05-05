<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\EmailVerification;
use App\Mail\OtpMail;
use SendGrid;
use SendGrid\Mail\Mail;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'status' => true,
            'data' => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'role' => 'nullable|in:user,admin,moderator',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bio' => $request->bio,
            'profile_picture' => $request->profile_picture,
            'phone_number' => $request->phone_number,
            'role' => $request->role ?? 'user',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'nullable|string|max:255|unique:users,username,' . $id,
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'role' => 'nullable|in:user,admin,moderator',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }

        if ($request->has('profile_picture')) {
            $user->profile_picture = $request->profile_picture;
        }

        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }

        if ($request->has('role')) {
            $user->role = $request->role;
        }

        if ($request->has('is_active')) {
            $user->is_active = $request->is_active;
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }

    //untuk diagram
    public function summary(Request $request)
    {
        $range = $request->query('range', '30days'); // default 30 days

        $days = 30;
        if (preg_match('/(\d+)days/', $range, $matches)) {
            $days = (int) $matches[1];
        }

        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Ambil user yang register dalam range waktu
        $registers = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Ambil user yang login (dengan asumsi kamu menyimpan last_login_at)
        $logins = User::whereBetween('last_login_at', [$startDate, $endDate])
            ->selectRaw('DATE(last_login_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Format untuk grafik
        $labels = [];
        $loginData = [];
        $registerData = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i)->toDateString();
            $labels[] = $date;
            $loginData[] = $logins[$date] ?? 0;
            $registerData[] = $registers[$date] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'login' => $loginData,
            'register' => $registerData,
        ]);
    }

    //send otp


    public function sendOtp(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email sudah diverifikasi']);
        }

        // Cek kalau baru saja kirim OTP
        $verif = EmailVerification::where('email', $user->email)->first();
        if ($verif && $verif->updated_at->diffInSeconds(now()) < 60) {
            return response()->json([
                'message' => 'Tunggu sebentar sebelum meminta OTP lagi.',
            ], 429);
        }

        $otp = rand(100000, 999999);

        EmailVerification::updateOrCreate(
            ['email' => $user->email],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(10)]
        );

        try {
            $email = new Mail();
            $email->setFrom("youremail@example.com", "Nama Aplikasi");
            $email->setSubject("Kode OTP Kamu");
            $email->addTo($user->email);
            $email->addContent(
                "text/plain",
                "Kode OTP kamu adalah: $otp"
            );

            $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
            $response = $sendgrid->send($email);

            return response()->json([
                'message' => 'Kode OTP berhasil dikirim (via SendGrid)',
                'status' => $response->statusCode(),
            ]);
        } catch (\Exception $e) {
            Log::error("SendGrid Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Gagal mengirim OTP via SendGrid: ' . $e->getMessage(),
            ], 500);
        }
    }
//

    public function verifyOtp(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan atau belum login'], 400);
        }

        // Tambahkan untuk debugging
        $configValues = [
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => substr(config('mail.mailers.smtp.password'), 0, 3) . '...'
        ];

        // Lanjutkan proses verifikasi OTP
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        // Cek OTP - pastikan model yang digunakan konsisten
        $otp = $request->input('otp');

        // Jika menggunakan EmailVerification model (sesuai dengan sendOtpVerification)
        $verification = EmailVerification::where('email', $user->email)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();


        if (!$verification) {
            return response()->json(['message' => 'OTP salah atau kadaluarsa'], 400);
        }

        // Update user dan set email_verified_at dan is_verified
        $user->email_verified_at = now();
        $user->is_verified = true;  // Kolom ini harus ada di tabel users
        $user->save();

        $user->refresh();
        $verification->delete();

        return response()->json([
            'message' => 'Email berhasil diverifikasi',
            'user' => $user
        ]);
    }
}
