<?php

namespace App\Http\Controllers;

use App\Services\TransactionProofStorage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DirectTransactionProofUploadController extends Controller
{
    public function create(Request $request, TransactionProofStorage $proof)
    {
        $validated = $request->validate([
            'folder' => ['required', Rule::in(['bukti_pemasukan','bukti_pengeluaran','dokumen_pendukung_honorarium'])],
            'filename' => ['required','string','max:255'],
            'content_type' => ['required', Rule::in(['image/jpeg','image/png','application/pdf'])],
            'size' => ['required','integer','min:1','max:5242880'],
        ]);
        $isJamaah = (bool)$request->user('jamaah');
        if ($isJamaah && $validated['folder'] !== 'bukti_pemasukan') abort(403);
        if (!$isJamaah && !$request->user('admin')) {
            $pegawai = $request->user('pegawai')?->pegawai;
            abort_unless($pegawai && $pegawai->akses_role === 'keuangan', 403);
        }
        // Dokumen belum mengubah data apa pun. Disimpan baru setelah transaksi lolos validasi.
        return response()->json($proof->prepareDirectUpload(
            $request, $validated['folder'], $validated['filename'],
            $validated['content_type'], (int)$validated['size']
        ));
    }
}
