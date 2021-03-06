<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Bidang;
use App\Models\Lamaran;
use App\Models\Lowongan;

use App\Supports\Api;

use Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(
                                Api $api,                                
                                Bidang $bidang,
                                Request $request,
                                Lamaran $lamaran,
    							Lowongan $lowongan
    							){
        $this->api      = $api;
        $this->bidang   = $bidang;
        $this->request  = $request;
    	$this->lamaran 	= $lamaran;
    	$this->lowongan = $lowongan;
    }

    public function index()
    {
        // return $this->formatWaktu()->dari .' ' . $this->formatWaktu()->ke;

        $namakota         = $this->namaKota(); 
        $bidang           = $this->bidang->get();
        $tahun            = Carbon::now()->format('Y');
        $tahun_sebelumnya = Carbon::now()->subYears(2)->format('Y'); 
        $lowongan         = $this->lowongan->kondisi()->paginate(5);     
        
    	return view('dashboard.index', compact(
            'lowongan', 'bidang', 'namakota', 'tahun', 'tahun_sebelumnya'
        ));
    }

    public function show($id)
    {
        if(Auth::user()){
            $user           = Auth::user()->id;
            $carilamaran    = $this->lamaran->where(['user_id' => $user, 'lowongan_id' => $id])->first();
            $kondisilamaran = $carilamaran;
        }else{
            $kondisilamaran = '';
        }

        

        $lowongan           = $this->lowongan->find($id);        
        $role               = Auth::user() ? Auth::user()->role : '';
        
        

        return view('dashboard.detail', compact('lowongan', 'role', 'kondisilamaran'));
    }

    public function namaKota()
    {
        $kota             = $this->api->kota()->getData();

        foreach ($kota->provinsi as $index => $item) {
            $namakota[] = $item->nama;
        }

        return $namakota;
    }
}
