<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Bunga;
use App\Models\IntroVidio;
use App\Models\Kurs;
use App\Models\Profil;
use App\Models\Promo;
use App\Models\Tenor;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getBunga()
    {
        $status = null;
        $message = null;
        $data = null;

        try {
            $data = Bunga::first()->bunga;
            
            $status = 200;
            $message = 'berhasil';
        }
        catch (\Exception $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        catch (\Illuminate\Database\QueryException $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        finally {
            $response = array(
                'status' => $status,
                'message' => $message,
                'data' => $data
            );

            return response($response, $status);
        }
    }

    public function getTenor()
    {
        $status = null;
        $message = null;
        $data = null;

        try {
            $data = Tenor::orderBy('tenor', 'ASC')->get();
            
            $status = 200;
            $message = 'berhasil';
        }
        catch (\Exception $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        catch (\Illuminate\Database\QueryException $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        finally {
            $response = array(
                'status' => $status,
                'message' => $message,
                'data' => $data
            );

            return response($response, $status);
        }
    }

    public function getKurs()
    {
        $status = null;
        $message = null;
        $data = null;

        try {
            $data = Kurs::select('id','nama', 'harga_beli', 'ket_beli', 'harga_jual', 'ket_jual', 'updated_at')
                        ->orderBy('nama', 'ASC')
                        ->get();
            
            $status = 200;
            $message = 'berhasil';
        }
        catch (\Exception $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        catch (\Illuminate\Database\QueryException $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        finally {
            $response = array(
                'status' => $status,
                'message' => $message,
                'data' => $data
            );

            return response($response, $status);
        }
    }

    public function getBerita(Request $request)
    {
        $status = null;
        $message = null;
        $data = null;

        try {
            $berita = Berita::select('id','judul', 'slug', 'cover', 'updated_at','konten','created_at')->orderBy('updated_at', 'ASC')->take(8)->get();

            $data['slide'] = [];
            $data['box'] = [];
            foreach ($berita as $key => $value) {
                $value->cover =  $request->getSchemeAndHttpHost()."/".$value->cover;
                $value->judul = substr($value->judul,0,60);
                $value->konten = substr($value->konten,0,100);
                $value->tgl = date('d M Y H:i',strtotime($value->created_at));
                if($key<=3){
                    array_push($data['slide'],$value);
                }
                else{
                    array_push($data['box'],$value);
                }
            }
            
            $status = 200;
            $message = 'berhasil';
        }
        catch (\Exception $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        catch (\Illuminate\Database\QueryException $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        finally {
            $response = array(
                'status' => $status,
                'message' => $message,
                'data' => $data
            );

            return response($response, $status);
        }
    }

    public function getProfil()
    {
        $status = null;
        $message = null;
        $data = null;

        try {
            $data = Profil::first();
            
            $status = 200;
            $message = 'berhasil';
        }
        catch (\Exception $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        catch (\Illuminate\Database\QueryException $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        finally {
            $response = array(
                'status' => $status,
                'message' => $message,
                'data' => $data
            );

            return response($response, $status);
        }
    }

    public function getVideo()
    {
        $status = null;
        $message = null;
        $data = null;

        try {
            $data = IntroVidio::first();
            
            $status = 200;
            $message = 'berhasil';
        }
        catch (\Exception $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        catch (\Illuminate\Database\QueryException $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        finally {
            $response = array(
                'status' => $status,
                'message' => $message,
                'data' => $data
            );

            return response($response, $status);
        }
    }

    public function getPromo(Request $request)
    {
        $status = null;
        $message = null;
        $data = null;

        try {
            $data = Promo::select('id','judul', 'slug', 'cover')->take(8)->get();

            foreach ($data as $key => $value) {
                $value->cover =  $request->getSchemeAndHttpHost()."/".$value->cover;
                $value->judul = substr($value->judul,0,15);
            }

            $status = 200;
            $message = 'berhasil';
        }
        catch (\Exception $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        catch (\Illuminate\Database\QueryException $e) {
            $status = 400;
            $message = 'gagal.'.$e->getMessage();
        }
        finally {
            $response = array(
                'status' => $status,
                'message' => $message,
                'data' => $data
            );

            return response($response, $status);
        }
    }
}
