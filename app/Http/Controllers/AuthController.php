<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    public function maintance()
    {
        return view('auth.maintance');
    }
    public function showRegister()
    {
        if (!\App\Models\PengaturanPendaftaran::aktif()) {
            return redirect('/')->with('error', 'Maaf, pendaftaran sudah di tutup.');
        }

        return view('auth.register');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Periksa apakah email sudah diverifikasi
            if (!$user->email_verified_at && $user->role === 'pendaftar') {
                Auth::logout();
                return redirect()->route('verify.otp', ['email' => $user->email])->with('error', 'Email Anda belum diverifikasi. Silakan masukkan kode OTP.');
            }

            // Periksa role user
            if ($user->role !== 'pendaftar') {
                Auth::logout();
                return back()->with('loginError', 'Area ini khusus pendaftar. Gunakan Login Internal.');
            }

            return redirect()->intended('/pendaftar/dashboard');
        }

        \Illuminate\Support\Facades\Log::warning('Login pendaftar gagal untuk email: ' . $request->email . ' dari IP: ' . $request->ip());
        return back()->with('loginError', 'Email atau password salah!');
    }

    public function showInternalLogin()
    {
        return view('auth.internal_login');
    }

    public function internalLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Blokir jika pendaftar mencoba login admin
            if (Auth::user()->role === 'pendaftar') {
                Auth::logout();
                return back()->with('loginError', 'Pendaftar tidak memiliki akses ke sini.');
            }

            if (Auth::user()->role === 'dosen') {
                return redirect()->intended('/admin/penilaian-akademik');
            }

            return redirect()->intended('/admin/dashboard');
        }

        \Illuminate\Support\Facades\Log::warning('Login internal/staff gagal untuk email: ' . $request->email . ' dari IP: ' . $request->ip());
        return back()->with('loginError', 'Kredensial salah atau tidak memiliki akses.');
    }

    public function register(Request $request)
    {
        if (!\App\Models\PengaturanPendaftaran::aktif()) {
            return redirect('/')->with('error', 'Maaf pendaftaran sudah di tutup.');
        }
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:peserta,nisn',
            'email' => 'required|string|email|max:255|unique:akun,email',
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/[A-Za-z]/', 'regex:/[0-9]/'],
            'no_whatsapp' => 'required|string|unique:peserta,no_whatsapp',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string',
            'tahun_lulus' => 'required|integer',
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'nama_sekolah' => 'required|string',
            'no_guru_bk' => 'required|string|max:30',
            'sumber_informasi' => 'required|string|max:100',
            'kode_referral' => 'nullable|string|max:50',
            'cf-turnstile-response' => 'required',
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain atau silakan login.',
            'nisn.unique' => 'NISN ini sudah terdaftar dalam sistem. Hubungi admin jika ini kesalahan.',
            'no_whatsapp.unique' => 'Nomor WhatsApp ini sudah digunakan oleh pendaftar lain.',
            'no_guru_bk.required' => 'Nomor WhatsApp Guru BK atau Wali Kelas wajib diisi.',
            'sumber_informasi.required' => 'Wajib memilih sumber informasi.',
            'cf-turnstile-response.required' => 'Wajib menyelesaikan verifikasi keamanan.',
            'password.regex' => 'Password harus mengandung kombinasi huruf dan angka.',
        ]);

        // --- Rate Limiting IP & Email (Anti-Spam) ---
        $throttleKey = 'register-otp-limit-' . sha1($request->ip() . '|' . strtolower($request->email));
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', 'Terlalu banyak permintaan dari perangkat Anda. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.')->withInput();
        }
        RateLimiter::hit($throttleKey, 3600); // Ban for 1 hour

        // Verify Cloudflare Turnstile
        $verifyResponse = \Illuminate\Support\Facades\Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => env('TURNSTILE_SECRET_KEY'),
            'response' => $request->input('cf-turnstile-response'),
            'remoteip' => $request->ip(),
        ]);

        if (!$verifyResponse->json('success')) {
            return back()->withErrors(['cf-turnstile-response' => 'Verifikasi robot gagal, silakan coba lagi.'])->withInput();
        }

        $email = $request->email;
        $nama = $request->nama;

        // 4. Store Registration Data in Session (Expires with session)
        session(['registration_payload' => $request->all()]);

        // 5. Generate & Send OTP menggunakan CSPRNG aman (6 digit)
        $otpCode = random_int(100000, 999999);
        \App\Models\Otp::updateOrCreate(
            ['email' => $email],
            [
                'otp' => (string) $otpCode,
                'expires_at' => now()->addMinutes(15) // Matching session typical window
            ]
        );

        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\OtpMail($nama, $otpCode));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim OTP: ' . $e->getMessage());
        }

        return redirect()->route('verify.otp', ['email' => $email])->with('success', 'Registrasi berhasil! Silakan masukkan kode OTP yang telah dikirim ke email Anda.');
    }

    public function showVerifyOtp(Request $request)
    {
        $email = $request->email;
        if (!$email) return redirect('/login');
        return view('auth.verify_otp', compact('email'));
    }

    public function verifyOtp(Request $request, \App\Services\CertificateService $certificateService)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $inputEmail = strtolower(trim($request->email));
        $inputOtp = trim($request->otp);

        $otpRecord = \App\Models\Otp::where('email', $inputEmail)
            ->where('otp', $inputOtp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.'])->withInput();
        }

        // Retrieve registration data from session
        $payload = session('registration_payload');
        
        if (!$payload || strtolower(trim($payload['email'])) !== $inputEmail) {
            return redirect('/register')->with('error', 'Data registrasi tidak ditemukan atau sudah kedaluwarsa. Silakan daftar ulang.');
        }

        // Create User & Related Records in a Transaction
        try {
            $user = \Illuminate\Support\Facades\DB::transaction(function () use ($payload) {
                // 1. Create Akun
                $user = Akun::create([
                    'nama' => $payload['nama'],
                    'email' => $payload['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($payload['password']),
                    'role' => 'pendaftar',
                    'email_verified_at' => now(), // Directly verified
                ]);

                // 2. Create Peserta
                $peserta = \App\Models\Peserta::create([
                    'akun_id' => $user->id,
                    'nama' => $payload['nama'],
                    'no_whatsapp' => $payload['no_whatsapp'],
                    'tgl_lahir' => $payload['tgl_lahir'],
                    'jenis_kelamin' => $payload['jenis_kelamin'],
                    'tahun_lulus' => $payload['tahun_lulus'],
                    'provinsi' => $payload['provinsi'],
                    'kabupaten' => $payload['kabupaten'],
                    'nama_sekolah' => $payload['nama_sekolah'],
                    'no_guru_bk' => $payload['no_guru_bk'] ?? null,
                    'nisn' => $payload['nisn'],
                ]);

                // 3. Create Daftar
                \App\Models\Daftar::create([
                    'peserta_id' => $peserta->id,
                    'no_wa' => $payload['no_whatsapp'],
                    'jenis_kelamin' => $payload['jenis_kelamin'],
                    'tahun_lulus' => $payload['tahun_lulus'],
                    'ttl' => $payload['tgl_lahir'],
                    'provinsi' => $payload['provinsi'],
                    'kabupaten' => $payload['kabupaten'],
                    'asal_sekolah' => $payload['nama_sekolah'],
                    'sumber_informasi' => $payload['sumber_informasi'] ?? null,
                    'kode_referral' => $payload['kode_referral'] ?? null,
                ]);

                return $user;
            });

            $otpRecord->delete(); // Cleanup
            session()->forget('registration_payload'); // Cleanup session

            // Auto Login
            Auth::login($user);

            return redirect('/pendaftar/dashboard')->with('success', 'Email berhasil diverifikasi! Selamat datang di dashboard MNCU Future Leader Scholarship.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Registration Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.');
        }
    }

    public function resendOtp(Request $request)
    {
        $email = $request->email;
        
        // --- Rate Limiting IP & Email (Anti-Spam) ---
        $throttleKey = 'resend-otp-limit-' . sha1($request->ip() . '|' . strtolower($email));
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', 'Terlalu banyak permintaan dari perangkat Anda. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.');
        }
        RateLimiter::hit($throttleKey, 3600); // Ban for 1 hour

        $otpCode = random_int(100000, 999999);
        \App\Models\Otp::updateOrCreate(
            ['email' => $email],
            [
                'otp' => (string) $otpCode,
                'expires_at' => now()->addMinutes(10)
            ]
        );

        try {
            $payload = session('registration_payload');
            
            if (!$payload) {
                return redirect('/register')->with('error', 'Sesi pendaftaran telah kedaluwarsa. Silakan isi kembali data pendaftaran Anda.');
            }

            $nama = $payload['nama'];
            
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\OtpMail($nama, $otpCode));
            return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Resend OTP Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email. Silakan coba lagi nanti.');
        }
    }

    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($request->email));

        // --- Rate Limiting IP & Email (Anti-Spam) ---
        $throttleKey = 'reset-limit-' . sha1($request->ip() . '|' . $email);
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', 'Terlalu banyak permintaan dari perangkat Anda. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.');
        }
        RateLimiter::hit($throttleKey, 3600); // Ban for 1 hour

        $user = Akun::where('email', $email)->first();
        if (!$user) {
            return back()->with('error', 'Email tidak terdaftar dalam sistem kami.');
        }

        // Generate Token
        $token = Str::random(64);

        // Save hashed token to password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => hash('sha256', $token),
                'created_at' => Carbon::now()
            ]
        );

        // Send Mail
        try {
            Mail::send('emails.reset_password', [
                'token' => $token,
                'user' => $user,
                'nama' => $user->nama,
                'linkReset' => url('reset-password/'.$token.'?email='.urlencode($user->email)),
                'templateBody' => \App\Models\EmailTemplate::renderBody('reset_password', [
                    'nama' => $user->nama,
                    'linkReset' => url('reset-password/'.$token.'?email='.urlencode($user->email)),
                ]),
            ], function($message) use($email, $user, $token){
                $message->to($email);
                $message->subject(\App\Models\EmailTemplate::subjectOrDefault('reset_password', 'Reset Password MNCU Future Leader Scholarship', [
                    'nama' => $user->nama,
                    'linkReset' => url('reset-password/'.$token.'?email='.urlencode($user->email)),
                ]));
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal kirim Reset Link: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email reset. Mohon coba lagi nanti.');
        }

        return back()->with('success', 'Kami telah mengirimkan link reset password ke email Anda.');
    }

    public function showResetPassword(Request $request, $token)
    {
        $email = $request->email;
        return view('auth.reset_password', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:akun,email',
            'token' => 'required',
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/[A-Za-z]/', 'regex:/[0-9]/'],
        ], [
            'password.regex' => 'Password baru harus mengandung kombinasi huruf dan angka.',
        ]);

        $resetData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where(function ($query) use ($request) {
                $query->where('token', hash('sha256', $request->token))
                      ->orWhere('token', $request->token);
            })
            ->first();

        if (!$resetData || Carbon::parse($resetData->created_at)->addMinutes(60)->isPast()) {
            if ($resetData) {
                DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();
            }
            return back()->with('error', 'Token reset tidak valid atau sudah kedaluwarsa.');
        }

        // Update Password
        $user = Akun::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete Token
        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect()->route('login')->with('success', 'Password Anda berhasil diperbarui! Silakan login kembali.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
