<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class PengaduanController extends Controller
{
    /**
     * Show the form for creating a new pengaduan (front-end).
     */
    public function create()
    {
        $categories = Pengaduan::getCategories();
        
        // Generate CAPTCHA
        session([
            'pengaduan_num1' => rand(1, 10),
            'pengaduan_num2' => rand(1, 10),
        ]);

        return view('front.pengaduan', compact('categories'));
    }

    /**
     * Store a newly created pengaduan from front-end.
     */
    public function store(Request $request)
    {
        // Validate CAPTCHA first
        $request->validate([
            'captcha' => 'required|numeric',
        ], [
            'captcha.required' => 'Silakan masukkan hasil penjumlahan.',
            'captcha.numeric' => 'Hasil penjumlahan harus berupa angka.',
        ]);

        // Check CAPTCHA
        $captchaAnswer = ($request->session()->get('pengaduan_num1', 0) + $request->session()->get('pengaduan_num2', 0));
        if ($request->captcha != $captchaAnswer) {
            return redirect()->back()
                ->withInput()
                ->with('captcha_error', 'Hasil penjumlahan CAPTCHA salah. Silakan coba lagi.');
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'kategori' => 'required|in:pelayanan,sarana,pegawai,produk_hukum,lainnya',
            'isi_pengaduan' => 'required|string|min:10|max:2000',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar|max:5120',
        ], [
            'nama.required' => 'Nama lengkap harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'no_hp.required' => 'Nomor HP harus diisi.',
            'kategori.required' => 'Kategori pengaduan harus dipilih.',
            'isi_pengaduan.required' => 'Isi pengaduan harus diisi.',
            'isi_pengaduan.min' => 'Isi pengaduan minimal 10 karakter.',
            'isi_pengaduan.max' => 'Isi pengaduan maksimal 2000 karakter.',
        ]);

        // Clear CAPTCHA after verification
        session()->forget(['pengaduan_num1', 'pengaduan_num2']);

        DB::beginTransaction();
        try {
            $data = $request->except('lampiran', 'captcha');

            // Handle file upload
            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                
                // Get file info before moving
                $fileSize = $file->getSize();
                $fileType = $file->getClientMimeType();
                $extension = $file->getClientOriginalExtension();
                
                $fileName = time() . '_' . uniqid() . '.' . $extension;
                $file->move(public_path('uploads/pengaduan'), $fileName);

                $data['lampiran'] = 'uploads/pengaduan/' . $fileName;
                $data['file_type'] = $fileType;
                $data['file_size'] = $fileSize;
            }

            // Set default status
            $data['status'] = 'pending';
            $data['tanggal_pengaduan'] = now();

            $pengaduan = Pengaduan::create($data);

            // Log activity
            ActivityLog::log(
                'Pengaduan masyarakat masuk',
                $pengaduan,
                'created',
                [
                    'no_pengaduan' => $pengaduan->no_pengaduan,
                    'nama' => $pengaduan->nama,
                    'kategori' => $pengaduan->kategori,
                ],
                'pengaduan'
            );

            DB::commit();

            // Store pengaduan number in session for success page
            session(['last_pengaduan_number' => $pengaduan->no_pengaduan]);

            return redirect()->route('pengaduan.success')->with('success', 'Pengaduan berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengirim pengaduan. Silakan coba lagi.');
        }
    }

    /**
     * Refresh CAPTCHA.
     */
    public function refreshCaptcha()
    {
        session([
            'pengaduan_num1' => rand(1, 10),
            'pengaduan_num2' => rand(1, 10),
        ]);

        return response()->json([
            'num1' => session('pengaduan_num1'),
            'num2' => session('pengaduan_num2'),
        ]);
    }

    /**
     * Show success page after submitting pengaduan.
     */
    public function success()
    {
        $noPengaduan = session('last_pengaduan_number');
        
        // Clear the session after displaying
        if ($noPengaduan) {
            session()->forget('last_pengaduan_number');
        }
        
        return view('front.pengaduan-success', compact('noPengaduan'));
    }

    /**
     * Track complaint by number (for modal).
     */
    public function track(Request $request)
    {
        $request->validate([
            'no_pengaduan' => 'required|string',
        ]);

        $pengaduan = Pengaduan::where('no_pengaduan', $request->no_pengaduan)->first();

        if (!$pengaduan) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor pengaduan tidak ditemukan.',
            ]);
        }

        $statusColors = [
            'pending' => 'yellow',
            'proses' => 'blue',
            'selesai' => 'green',
            'ditolak' => 'red',
        ];

        $statusLabels = [
            'pending' => 'Pending',
            'proses' => 'Dalam Proses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'no_pengaduan' => $pengaduan->no_pengaduan,
                'nama' => $pengaduan->nama,
                'email' => $pengaduan->email,
                'kategori' => ucfirst($pengaduan->kategori),
                'isi_pengaduan' => $pengaduan->isi_pengaduan,
                'status' => $pengaduan->status,
                'status_label' => $statusLabels[$pengaduan->status],
                'status_color' => $statusColors[$pengaduan->status],
                'tanggal_pengaduan' => $pengaduan->tanggal_pengaduan->isoFormat('D MMMM Y, HH:mm'),
                'respon' => $pengaduan->respon,
                'tanggal_respon' => $pengaduan->tanggal_respon ? $pengaduan->tanggal_respon->isoFormat('D MMMM Y') : null,
            ],
        ]);
    }
}
