<?php

namespace App\Http\Controllers\backend;

use File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TataKelolaPerusahaan;

class TataKelolaPerusahaanController extends Controller
{
    private $param;
    private $menu;

    public function __construct(){
        $this->param['title'] = 'Tata Kelola Perusahaan';
        $this->param['pageIcon']='briefcase';
        $this->param['pageTitle']='Tata Kelola Perusahaan';
        $this->menu = 'Master Tata Kelola Perusahaan';
    }

    public function index(Request $request){
        if($this->hasPermission($this->menu)){
            $this->param['btnRight']['text'] = 'Tambah';
            $this->param['btnRight']['link'] = route('tata-kelola-perusahaan.create');
            // $this->param['data'] = [];
            try {
                $keyword = $request->get('keyword');
                $getLaporan = TataKelolaPerusahaan::select('tata_kelola_perusahaan.*', 'users.name')
                                            ->join('users', 'users.id', 'tata_kelola_perusahaan.user_id')
                                            ->orderBy('tahun', 'DESC');
    
                if ($keyword) {
                    $getLaporan->where('tahun', 'LIKE', "%$keyword%");
                }
    
                $this->param['data'] = $getLaporan->paginate(10);
            } catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withStatus('Terjadi Kesalahan');
            }
    
            return view('backend.tata_kelola.index',$this->param);
        } else return view('error_page.forbidden');
    }

    public function create()
    {
        if($this->hasPermission($this->menu)){
            $this->param['btnRight']['text'] = 'Lihat Data';
            $this->param['btnRight']['link'] = route('tata-kelola-perusahaan.index');
    
            return \view('backend.tata_kelola.create', $this->param);
        } else return view('error_page.forbidden');
    }

    public function store(Request $request){
        if($this->hasPermission($this->menu)){
            $validatedData = $request->validate(
                [
                    'tahun' => 'required|unique:tata_kelola_perusahaan,tahun',
                    'title' => 'required',
                    'cover' => 'required|file|max:2048|mimes:jpeg,jpg',
                    'laporan' => 'required|file|max:10240|mimes:pdf',
                ],
                [
                    'required' => ':attribute tidak boleh kosong.',
                    'unique' => ':attribute telah tersedia.',
                    'mimes' => ':attribute hanya dapat menerima file jpeg, jpg.',
                    'file' => ':attribute harus berbentuk file.',
                    'max' => 'Maksimal ukuran file hingga 10mb.'
                ],
                [
                    'tahun' => 'Tahun',
                    'title' => 'Title',
                    'cover' => 'Cover',
                    'laporan' => 'File Tata Kelola Perusahaan'
                ]
            );
    
            try {
                if($request->file('cover') != null) {
                    $folder = 'public/upload/tata-kelola-perusahaan/';
                    $file = $request->file('cover');
                    $filename = date('YmdHis').str_replace(' ', '_', $file->getClientOriginalName());
                    // Get canonicalized absolute pathname
                    $path = realpath($folder);
    
                    // If it exist, check if it's a directory
                    if(!($path !== true AND is_dir($path)))
                    {
                        // Path/folder does not exist then create a new folder
                        mkdir($folder, 0755, true);
                    }
                    $coverCompressed = \Image::make($file->getRealPath());
    
                    if($coverCompressed->save($folder.$filename, 50)) {
                        if($request->file('laporan') != null) {
                            $folderL = 'public/upload/tata-kelola-perusahaan/';
                            $fileL = $request->file('laporan');
                            $filenameL = date('YmdHis').str_replace(' ', '_', $fileL->getClientOriginalName());
                            // Get canonicalized absolute pathname
                            $pathL = realpath($folderL);
    
                            // If it exist, check if it's a directory
                            if(!($pathL !== true AND is_dir($pathL)))
                            {
                                // Path/folder does not exist then create a new folder
                                mkdir($folderL, 0755, true);
                            }
                            if($fileL->move($folderL, $filenameL)) {
                                $newLaporan = new TataKelolaPerusahaan;
    
                                $newLaporan->tahun = $request->get('tahun');
                                $newLaporan->title = $request->get('title');
                                $newLaporan->cover = $folder.$filename;
                                $newLaporan->file = $folder.$filenameL;
                                $newLaporan->user_id = auth()->user()->id;
    
                                $newLaporan->save();
                            }
                        }
                    }
                }
    
                return redirect()->route('tata-kelola-perusahaan.index')->withStatus('Data berhasil ditambahkan.');
            } catch (\Exception $e) {
                return back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
            } catch (\Illuminate\Database\QueryException $e) {
                return back()->withError('Terjadi kesalahan pada database : ' . $e->getMessage());
            }
        } else return view('error_page.forbidden');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if($this->hasPermission($this->menu)){
            try {
                $this->param['btnRight']['text'] = 'Lihat Data';
                $this->param['btnRight']['link'] = route('tata-kelola-perusahaan.index');
                $this->param['laporan'] = TataKelolaPerusahaan::find($id);
    
                return \view('backend.tata_kelola.edit', $this->param);
            }
            catch (\Exception $e) {
                return redirect()->back()->withError('Terjadi kesalahan');
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withError('Terjadi kesalahan');
            }
        } else return view('error_page.forbidden');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($this->hasPermission($this->menu)){
            $laporan = TataKelolaPerusahaan::find($id);
            $isUnique = $laporan->tahun == $request->get('tahun') ? '' : '|unique:tata_kelola_perusahaan,tahun';
            $validCover = $request->file('cover') != null ? 'file|max:2048|mimes:jpeg,jpg' : '';
            $validFile = $request->file('laporan') != null ? 'file|max:10240|mimes:pdf' : '';
    
            $validatedData = $request->validate(
                [
                    'tahun' => 'required'.$isUnique,
                    'cover' => $validCover,
                    'laporan' => $validFile,
                    'title' => 'required'
                ],
                [
                    'required' => ':attribute tidak boleh kosong.',
                    'unique' => ':attribute telah tersedia.',
                    'mimes' => ':attribute hanya dapat menerima file pdf.',
                    'file' => ':attribute harus berbentuk file.',
                    'max' => 'Maksimal ukuran file hingga 10mb.'
                ],
                [
                    'tahun' => 'Tahun',
                    'title' => 'Title',
                    'cover' => 'Cover',
                    'laporan' => 'File Laporan Keuangan',
                ]
            );
    
            try {
                if($request->file('cover') != null) {
                    if($laporan->cover != null) {
                        // mengecek apakah file sebelumnya ada atau tidak
                        if(file_exists($laporan->cover)){
                            // Menghapus file sebelumnya
                            if(File::delete($laporan->cover)) {
                                $folder = 'public/upload/tata-kelola-perusahaan/';
                                $file = $request->file('cover');
                                $filename = date('YmdHis').$file->getClientOriginalName();
                                // Get canonicalized absolute pathname
                                $path = realpath($folder);
    
                                // If it exist, check if it's a directory
                                if(!($path !== true AND is_dir($path)))
                                {
                                    // Path/folder does not exist then create a new folder
                                    mkdir($folder, 0755, true);
                                }
                                $coverCompressed = \Image::make($file->getRealPath());
    
                                if($coverCompressed->save($folder.$filename, 50)) {
                                    $laporan->cover = $folder.$filename;
                                }
                            }
                        }
                    }
                    else {
                        $folder = 'public/upload/tata-kelola-perusahaan/';
                        $file = $request->file('cover');
                        $filename = date('YmdHis').$file->getClientOriginalName();
                        // Get canonicalized absolute pathname
                        $path = realpath($folder);
    
                        // If it exist, check if it's a directory
                        if(!($path !== true AND is_dir($path)))
                        {
                            // Path/folder does not exist then create a new folder
                            mkdir($folder, 0755, true);
                        }
                        $coverCompressed = \Image::make($file->getRealPath());
    
                        if($coverCompressed->save($folder.$filename, 50)) {
                            $laporan->cover = $folder.$filename;
                        }
                    }
                }
                if($request->file('laporan') != null) {
                    if($laporan->file != null) {
                        // mengecek apakah file sebelumnya ada atau tidak
                        if(file_exists($laporan->file)){
                            // Menghapus file sebelumnya
                            if(File::delete($laporan->file)) {
                                $folderL = 'public/upload/tata-kelola-perusahaan/';
                                $fileL = $request->file('laporan');
                                $filenameL = date('YmdHis').$fileL->getClientOriginalName();
                                // Get canonicalized absolute pathname
                                $pathL = realpath($folderL);
    
                                // If it exist, check if it's a directory
                                if(!($pathL !== true AND is_dir($pathL)))
                                {
                                    // Path/folder does not exist then create a new folder
                                    mkdir($folderL, 0755, true);
                                }
                                if($fileL->move($folderL, $filenameL)) {
                                    $laporan->file = $folderL.$filenameL;
                                }
                            }
                        }
                    }
                    else {
                        $folderL = 'public/upload/tata-kelola-perusahaan/';
                        $fileL = $request->file('laporan');
                        $filenameL = date('YmdHis').$fileL->getClientOriginalName();
                        // Get canonicalized absolute pathname
                        $pathL = realpath($folderL);
    
                        // If it exist, check if it's a directory
                        if(!($pathL !== true AND is_dir($pathL)))
                        {
                            // Path/folder does not exist then create a new folder
                            mkdir($folderL, 0755, true);
                        }
                        if($fileL->move($folderL, $filenameL)) {
                            $laporan->file = $folderL.$filenameL;
                        }
                    }
                }
    
                $laporan->tahun = $request->get('tahun');
                $laporan->title = $request->get('title');
                $laporan->user_id = auth()->user()->id;
    
                $laporan->save();
    
                return redirect()->route('tata-kelola-perusahaan.index')->withStatus('Data berhasil disimpan.');
            } catch (\Exception $e) {
                return back()->withError('Terjadi kesalahan. : ' . $e->getMessage());
            } catch (\Illuminate\Database\QueryException $e) {
                return back()->withError('Terjadi kesalahan pada database : ' . $e->getMessage());
            }
        } else return view('error_page.forbidden');
    }

    public function destroy($id){
        if($this->hasPermission($this->menu)){
            try{
                $laporan = TataKelolaPerusahaan::find($id);
    
                $cover = $laporan->cover;
                if($cover != null){
                    if(file_exists($cover)){
                        File::delete($cover);
                    }
                }
    
                $file = $laporan->file;
                if($file != null){
                    if(file_exists($file)){
                        if(File::delete($file)){
                            $laporan->delete();
                        }
                    }
                }
    
                if(!file_exists($cover) && !file_exists($file))
                    $laporan->delete();
    
                return redirect()->back()->withStatus('Berhasil menghapus data.');
            }
            catch(\Exception $e){
                return redirect()->back()->withError($e->getMessage());
            }
            catch(\Illuminate\Database\QueryException $e){
                return redirect()->back()->withError($e->getMessage());
            }
        } else return view('error_page.forbidden');
    }
}
