<?php

namespace App\Http\Controllers;

use App\Models\AgendaKegiatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaKegiatanController extends Controller
{
    /**
     * Daftar semua agenda kegiatan.
     */
    public function index(): View
    {
        $agenda = AgendaKegiatan::orderBy('urutan')->orderBy('id')->get();

        return view('admin.agenda-kegiatan.index', [
            'agenda'         => $agenda,
            'kategoriLabels' => AgendaKegiatan::kategoriLabels(),
        ]);
    }

    /**
     * Form tambah agenda baru.
     */
    public function create(): View
    {
        return view('admin.agenda-kegiatan.create', [
            'kategoriLabels' => AgendaKegiatan::kategoriLabels(),
        ]);
    }

    /**
     * Simpan agenda baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:150',
            'kategori'  => 'required|in:kajian,sosial,pendidikan,ibadah',
            'hari'      => 'required|string|max:100',
            'waktu'     => 'required|string|max:100',
            'lokasi'    => 'required|string|max:150',
            'deskripsi' => 'nullable|string|max:500',
            'urutan'    => 'nullable|integer|min:0|max:999',
            'is_aktif'  => 'boolean',
        ]);

        $validated['is_aktif'] = $request->boolean('is_aktif', true);
        $validated['urutan']   = $validated['urutan'] ?? 0;

        AgendaKegiatan::create($validated);

        return redirect()
            ->route('admin.agenda-kegiatan.index')
            ->with('success', 'Agenda kegiatan berhasil ditambahkan.');
    }

    /**
     * Form edit agenda yang sudah ada.
     */
    public function edit(AgendaKegiatan $agendaKegiatan): View
    {
        return view('admin.agenda-kegiatan.edit', [
            'agenda'         => $agendaKegiatan,
            'kategoriLabels' => AgendaKegiatan::kategoriLabels(),
        ]);
    }

    /**
     * Simpan perubahan agenda.
     */
    public function update(Request $request, AgendaKegiatan $agendaKegiatan): RedirectResponse
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:150',
            'kategori'  => 'required|in:kajian,sosial,pendidikan,ibadah',
            'hari'      => 'required|string|max:100',
            'waktu'     => 'required|string|max:100',
            'lokasi'    => 'required|string|max:150',
            'deskripsi' => 'nullable|string|max:500',
            'urutan'    => 'nullable|integer|min:0|max:999',
            'is_aktif'  => 'boolean',
        ]);

        $validated['is_aktif'] = $request->boolean('is_aktif', false);
        $validated['urutan']   = $validated['urutan'] ?? 0;

        $agendaKegiatan->update($validated);

        return redirect()
            ->route('admin.agenda-kegiatan.index')
            ->with('success', 'Agenda kegiatan berhasil diperbarui.');
    }

    /**
     * Hapus agenda.
     */
    public function destroy(AgendaKegiatan $agendaKegiatan): RedirectResponse
    {
        $agendaKegiatan->delete();

        return redirect()
            ->route('admin.agenda-kegiatan.index')
            ->with('success', 'Agenda kegiatan berhasil dihapus.');
    }

    /**
     * Toggle aktif/nonaktif secara cepat.
     */
    public function toggleAktif(AgendaKegiatan $agendaKegiatan): RedirectResponse
    {
        $agendaKegiatan->update(['is_aktif' => ! $agendaKegiatan->is_aktif]);

        $label = $agendaKegiatan->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.agenda-kegiatan.index')
            ->with('success', "Agenda \"{$agendaKegiatan->judul}\" berhasil {$label}.");
    }
}
