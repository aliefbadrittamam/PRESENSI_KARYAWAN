<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Karyawan;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\Presensi;

class FileManagerController extends Controller
{
    protected $allowedDirectories = [
        'FilePendukung',
        'foto-karyawan',
        'FotoPresensi'
    ];

    public function index(Request $request)
    {
        $directory = $request->get('dir', '');
        $search = $request->get('search', '');
        $fileType = $request->get('type', '');
        
        // Security check
        if (!empty($directory) && !$this->isAllowedDirectory($directory)) {
            return redirect()->route('admin.file-manager.index')
                           ->with('error', 'Akses ke direktori tidak diizinkan.');
        }

        $files = [];
        $directories = [];
        $storageUsage = [];

        // Get base directories
        if (empty($directory)) {
            foreach ($this->allowedDirectories as $dir) {
                if (Storage::disk('public')->exists($dir)) {
                    $directories[] = [
                        'name' => $dir,
                        'path' => $dir,
                        'size' => $this->getDirectorySize($dir),
                        'files_count' => $this->countFiles($dir),
                        'modified' => Storage::disk('public')->lastModified($dir)
                    ];
                }
            }
            
            // Calculate storage usage
            $storageUsage = $this->getStorageUsage();
        } else {
            // Get files and subdirectories in current directory
            $path = storage_path('app/public/' . $directory);
            
            if (File::exists($path)) {
                $items = File::allFiles($path);
                
                foreach ($items as $item) {
                    $relativePath = str_replace(storage_path('app/public/'), '', $item->getPathname());
                    
                    // Filter by search
                    if (!empty($search) && stripos($item->getFilename(), $search) === false) {
                        continue;
                    }
                    
                    // Filter by type
                    if (!empty($fileType)) {
                        $extension = strtolower($item->getExtension());
                        if ($fileType === 'image' && !in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            continue;
                        }
                        if ($fileType === 'document' && !in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx'])) {
                            continue;
                        }
                    }
                    
                    $files[] = [
                        'name' => $item->getFilename(),
                        'path' => $relativePath,
                        'size' => $item->getSize(),
                        'extension' => $item->getExtension(),
                        'modified' => $item->getMTime(),
                        'url' => Storage::url($relativePath)
                    ];
                }
                
                // Sort by modified time (newest first)
                usort($files, function($a, $b) {
                    return $b['modified'] - $a['modified'];
                });
            }
        }

        return view('admin.file-manager.index', compact(
            'files',
            'directories',
            'directory',
            'search',
            'fileType',
            'storageUsage'
        ));
    }

    public function show($path)
    {
        // Decode path
        $filePath = urldecode($path);
        
        // Security check
        $directory = dirname($filePath);
        if (!$this->isAllowedDirectory($directory)) {
            abort(403, 'Akses ditolak');
        }

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $fullPath = storage_path('app/public/' . $filePath);
        $fileInfo = [
            'name' => basename($filePath),
            'path' => $filePath,
            'size' => File::size($fullPath),
            'extension' => File::extension($fullPath),
            'modified' => File::lastModified($fullPath),
            'url' => Storage::url($filePath),
            'mime_type' => File::mimeType($fullPath)
        ];

        // Get additional info based on directory
        $directory = explode('/', $filePath)[0];
        $additionalInfo = $this->getFileAdditionalInfo($filePath, $directory);

        return view('admin.file-manager.show', compact('fileInfo', 'directory', 'additionalInfo'));
    }

    public function download($path)
    {
        $filePath = urldecode($path);
        
        // Security check
        $directory = dirname($filePath);
        if (!$this->isAllowedDirectory($directory)) {
            abort(403, 'Akses ditolak');
        }

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($filePath);
    }

    public function delete(Request $request)
    {
        $path = $request->input('path');
        
        if (!$this->isAllowedDirectory(dirname($path))) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return response()->json(['success' => true, 'message' => 'File berhasil dihapus']);
        }

        return response()->json(['success' => false, 'message' => 'File tidak ditemukan'], 404);
    }

    public function bulkDelete(Request $request)
    {
        $paths = $request->input('paths', []);
        $deleted = 0;

        foreach ($paths as $path) {
            if ($this->isAllowedDirectory(dirname($path)) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                $deleted++;
            }
        }

        return response()->json([
            'success' => true, 
            'message' => "$deleted file(s) berhasil dihapus"
        ]);
    }

    private function getFileAdditionalInfo($filePath, $directory)
    {
        $fileName = basename($filePath);
        $info = [];

        switch ($directory) {
            case 'foto-karyawan':
                $karyawan = Karyawan::where('foto', 'like', '%'.$fileName)->first();
                if ($karyawan) {
                    $info = [
                        'type' => 'karyawan',
                        'nama_lengkap' => $karyawan->nama_lengkap,
                        'nip' => $karyawan->nip,
                        'departemen' => $karyawan->departemen->nama_departemen ?? '-',
                        'jabatan' => $karyawan->jabatan->nama_jabatan ?? '-',
                    ];
                }
                break;

            case 'FilePendukung':
                $izin = Izin::where('file_pendukung', 'like', '%'.$fileName)->first();
                $cuti = Cuti::where('file_pendukung', 'like', '%'.$fileName)->first();
                
                if ($izin) {
                    $info = [
                        'type' => 'izin',
                        'tipe_izin' => ucfirst($izin->tipe_izin),
                        'karyawan' => $izin->karyawan->nama_lengkap ?? '-',
                        'nip' => $izin->karyawan->nip ?? '-',
                        'tanggal_mulai' => $izin->tanggal_mulai,
                        'tanggal_selesai' => $izin->tanggal_selesai,
                        'keterangan' => $izin->keterangan,
                        'status' => $izin->status_approval,
                    ];
                } elseif ($cuti) {
                    $info = [
                        'type' => 'cuti',
                        'jenis_cuti' => ucfirst($cuti->jenis_cuti),
                        'karyawan' => $cuti->karyawan->nama_lengkap ?? '-',
                        'nip' => $cuti->karyawan->nip ?? '-',
                        'tanggal_mulai' => $cuti->tanggal_mulai,
                        'tanggal_selesai' => $cuti->tanggal_selesai,
                        'keterangan' => $cuti->keterangan,
                        'status' => $cuti->status_approval,
                    ];
                }
                break;

            case 'FotoPresensi':
                // Parse dari nama file atau cari di database
                if (preg_match('/(\d{2}-\d{2}-\d{4})_\d{6}_(.+?)\((.+?)\)/', $fileName, $matches)) {
                    $tanggal = \Carbon\Carbon::createFromFormat('d-m-Y', $matches[1])->format('Y-m-d');
                    $namaKaryawan = $matches[2];
                    $nip = $matches[3];
                    
                    // Deteksi tipe dari path
                    $tipeAbsen = 'Unknown';
                    if (strpos($filePath, '/Masuk/') !== false) {
                        $tipeAbsen = 'Masuk';
                    } elseif (strpos($filePath, '/Keluar/') !== false) {
                        $tipeAbsen = 'Keluar';
                    }
                    
                    // Cari presensi
                    $presensi = Presensi::whereDate('tanggal_presensi', $tanggal)
                        ->whereHas('karyawan', function($q) use ($nip) {
                            $q->where('nip', $nip);
                        })
                        ->first();
                    
                    $info = [
                        'type' => 'presensi',
                        'tipe_absen' => $tipeAbsen,
                        'tanggal' => $tanggal,
                        'karyawan' => $namaKaryawan,
                        'nip' => $nip,
                    ];
                    
                    if ($presensi) {
                        $info['jam_masuk'] = $presensi->jam_masuk;
                        $info['jam_keluar'] = $presensi->jam_keluar;
                        $info['status_kehadiran'] = $presensi->status_kehadiran;
                    }
                }
                break;
        }

        return $info;
    }

    private function isAllowedDirectory($directory)
    {
        foreach ($this->allowedDirectories as $allowed) {
            if (strpos($directory, $allowed) === 0) {
                return true;
            }
        }
        return false;
    }

    private function getDirectorySize($directory)
    {
        $size = 0;
        $path = storage_path('app/public/' . $directory);
        
        if (File::exists($path)) {
            foreach (File::allFiles($path) as $file) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }

    private function countFiles($directory)
    {
        $path = storage_path('app/public/' . $directory);
        
        if (File::exists($path)) {
            return count(File::allFiles($path));
        }
        
        return 0;
    }

    private function getStorageUsage()
    {
        $usage = [];
        $totalSize = 0;
        
        foreach ($this->allowedDirectories as $dir) {
            $size = $this->getDirectorySize($dir);
            $totalSize += $size;
            
            $usage[] = [
                'name' => $dir,
                'size' => $size,
                'formatted' => $this->formatBytes($size),
                'files_count' => $this->countFiles($dir)
            ];
        }
        
        return [
            'directories' => $usage,
            'total' => $totalSize,
            'total_formatted' => $this->formatBytes($totalSize)
        ];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}