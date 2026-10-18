<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormBSController extends Controller
{
    public function index()
    {
        // Ambil data dari tabel pengajuan BS (sesuaikan nama tabel jika berbeda, misal 'pengajuan_bs')
        $pengajuans = DB::table('pengajuan_bs_items')->orderBy('id', 'desc')->get();

        return view('pengajuan_bs.index', compact('pengajuans'));
    }
}