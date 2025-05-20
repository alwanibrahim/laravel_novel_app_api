<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Mail; // Pastikan baris ini ada



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
            'profile_picture' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
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

        if ($request->hasFile('profile_picture')) {
    // Hapus gambar lama jika ada
    if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
        Storage::disk('public')->delete($user->profile_picture);
    }

    // Simpan file baru ke storage/app/public/profile_pictures
    $path = $request->file('profile_picture')->store('profile_picture', 'public');
    $user->profile_picture = $path;
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
        // Pastikan user sudah terautentikasi
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan atau belum login'], 400);
        }

        // Ambil email dari user yang terautentikasi
        $email = $user->email;

        // Buat OTP 6 digit
        $otp = rand(100000, 999999);

        // Simpan atau update OTP di database menggunakan updateOrCreate
        EmailVerification::updateOrCreate(
            ['email' => $email],  // Kondisi pencarian berdasarkan email
            ['otp' => $otp, 'expires_at' => now()->addMinutes(10)]  // Data yang diupdate atau dibuat baru
        );

        // Kirim email dengan OTP
        Notification::route('mail', $email)->notify(new OtpNotification($otp));

        return response()->json(['message' => 'OTP berhasil dikirim']);
    }


    public function verifyOtp(Request $request)
    {
        // Ambil user yang terautentikasi
        $user = $request->user();

        // Pastikan user sudah terautentikasi
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan atau belum login'], 400);
        }

        // Validasi input OTP
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        // Ambil OTP yang dikirimkan
        $otp = $request->input('otp');

        // Cek apakah OTP valid dan belum kadaluarsa
        $verification = EmailVerification::where('email', $user->email)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())  // Pastikan OTP belum kadaluarsa
            ->first();

        if (!$verification) {
            return response()->json(['message' => 'OTP salah atau kadaluarsa'], 400);
        }

        // Update status email user
        $user->email_verified_at = now();
        $user->is_verified = true;  // Kolom ini harus ada di tabel users
        $user->save();

        // Refresh user untuk memastikan status terbaru
        $user->refresh();

        // Hapus data OTP setelah verifikasi sukses
        $verification->delete();

        // Kembalikan response sukses
        return response()->json([
            'message' => 'Email berhasil diverifikasi',
            'user' => $user
        ]);
    }
}
