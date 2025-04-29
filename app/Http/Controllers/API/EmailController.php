<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\UserNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    // API untuk mengirim email ke user tertentu
    public function sendEmailToUsers(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'subject' => $request->subject,
            'title' => $request->title,
            'body' => $request->body,
        ];

        $successCount = 0;
        $failedEmails = [];

        // Kirim email ke user yang dipilih
        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);
            try {
                Mail::to($user->email)->send(new UserNotification($data));
                $successCount++;
            } catch (\Exception $e) {
                $failedEmails[] = [
                    'user_id' => $userId,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Email terkirim ke $successCount pengguna",
            'failed_emails' => $failedEmails
        ]);
    }

    // API untuk mengirim email ke semua user
    public function sendEmailToAllUsers(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'subject' => $request->subject,
            'title' => $request->title,
            'body' => $request->body,
        ];

        $users = User::all();
        $successCount = 0;
        $failedEmails = [];

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new UserNotification($data));
                $successCount++;
            } catch (\Exception $e) {
                $failedEmails[] = [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Email terkirim ke $successCount dari " . count($users) . " pengguna",
            'failed_emails' => $failedEmails
        ]);
    }

    // API untuk mendapatkan daftar semua user (untuk dipilih)
    public function getAllUsers()
    {
        $users = User::select('id', 'name', 'email')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
