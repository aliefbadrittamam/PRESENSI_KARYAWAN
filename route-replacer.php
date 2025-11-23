<?php
/**
 * Script untuk auto-replace route di semua file blade
 * Jalankan: php route-replacer.php
 * 
 * PERHATIAN: Backup file Anda sebelum menjalankan script ini!
 */

// Daftar route yang perlu diganti
$routeReplacements = [
    // Fakultas
    "route('fakultas.index')" => "route('admin.fakultas.index')",
    "route('fakultas.create')" => "route('admin.fakultas.create')",
    "route('fakultas.store')" => "route('admin.fakultas.store')",
    "route('fakultas.show'" => "route('admin.fakultas.show'",
    "route('fakultas.edit'" => "route('admin.fakultas.edit'",
    "route('fakultas.update'" => "route('admin.fakultas.update'",
    "route('fakultas.destroy'" => "route('admin.fakultas.destroy'",
    
    // Departemen
    "route('departemen.index')" => "route('admin.departemen.index')",
    "route('departemen.create')" => "route('admin.departemen.create')",
    "route('departemen.store')" => "route('admin.departemen.store')",
    "route('departemen.show'" => "route('admin.departemen.show'",
    "route('departemen.edit'" => "route('admin.departemen.edit'",
    "route('departemen.update'" => "route('admin.departemen.update'",
    "route('departemen.destroy'" => "route('admin.departemen.destroy'",
    
    // Jabatan
    "route('jabatan.index')" => "route('admin.jabatan.index')",
    "route('jabatan.create')" => "route('admin.jabatan.create')",
    "route('jabatan.store')" => "route('admin.jabatan.store')",
    "route('jabatan.show'" => "route('admin.jabatan.show'",
    "route('jabatan.edit'" => "route('admin.jabatan.edit'",
    "route('jabatan.update'" => "route('admin.jabatan.update'",
    "route('jabatan.destroy'" => "route('admin.jabatan.destroy'",
    
    // Karyawan
    "route('karyawan.index')" => "route('admin.karyawan.index')",
    "route('karyawan.create')" => "route('admin.karyawan.create')",
    "route('karyawan.store')" => "route('admin.karyawan.store')",
    "route('karyawan.show'" => "route('admin.karyawan.show'",
    "route('karyawan.edit'" => "route('admin.karyawan.edit'",
    "route('karyawan.update'" => "route('admin.karyawan.update'",
    "route('karyawan.destroy'" => "route('admin.karyawan.destroy'",
    
    // Shift
    "route('shift.index')" => "route('admin.shift.index')",
    "route('shift.create')" => "route('admin.shift.create')",
    "route('shift.store')" => "route('admin.shift.store')",
    "route('shift.show'" => "route('admin.shift.show'",
    "route('shift.edit'" => "route('admin.shift.edit'",
    "route('shift.update'" => "route('admin.shift.update'",
    "route('shift.destroy'" => "route('admin.shift.destroy'",
    
    // Presensi Admin
    "route('presensi.index')" => "route('admin.presensi.index')",
    "route('presensi.rekap')" => "route('admin.presensi.rekap')",
    "route('presensi.show'" => "route('admin.presensi.show'",
    "route('presensi.download-pdf')" => "route('admin.presensi.download-pdf')",
    
    // Home -> Dashboard
    "route('home')" => "route('admin.dashboard')",
];

// Folder yang akan di-scan
$viewsPath = __DIR__ . '/resources/views';

// Fungsi untuk scan semua file blade
function scanDirectory($dir, &$files = []) {
    if (!is_dir($dir)) {
        echo "❌ Folder tidak ditemukan: $dir\n";
        return $files;
    }
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        
        $path = $dir . '/' . $item;
        
        if (is_dir($path)) {
            scanDirectory($path, $files);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $files[] = $path;
        }
    }
    
    return $files;
}

// Fungsi untuk replace content
function replaceInFile($file, $replacements) {
    $content = file_get_contents($file);
    $originalContent = $content;
    $replacedCount = 0;
    
    foreach ($replacements as $old => $new) {
        $count = 0;
        $content = str_replace($old, $new, $content, $count);
        $replacedCount += $count;
    }
    
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        return $replacedCount;
    }
    
    return 0;
}

// Main execution
echo "🚀 Starting route replacement...\n\n";
echo "📂 Scanning directory: $viewsPath\n\n";

if (!is_dir($viewsPath)) {
    echo "❌ ERROR: Views folder not found!\n";
    echo "   Make sure you run this script from Laravel root directory.\n";
    exit(1);
}

// Scan all blade files
$files = scanDirectory($viewsPath);
echo "📄 Found " . count($files) . " PHP files\n\n";

// Process each file
$totalReplacements = 0;
$filesModified = 0;

echo "🔄 Processing files...\n";
echo str_repeat("-", 80) . "\n";

foreach ($files as $file) {
    $relativePath = str_replace($viewsPath, 'resources/views', $file);
    $count = replaceInFile($file, $routeReplacements);
    
    if ($count > 0) {
        $filesModified++;
        $totalReplacements += $count;
        echo "✅ {$relativePath}\n";
        echo "   → Replaced {$count} route(s)\n";
    }
}

echo str_repeat("-", 80) . "\n";
echo "\n✨ DONE!\n\n";
echo "📊 Summary:\n";
echo "   - Files scanned: " . count($files) . "\n";
echo "   - Files modified: {$filesModified}\n";
echo "   - Total replacements: {$totalReplacements}\n\n";

if ($filesModified > 0) {
    echo "✅ Route replacement completed successfully!\n";
    echo "\n📝 Next steps:\n";
    echo "   1. Review the changes in your files\n";
    echo "   2. Run: php artisan route:clear\n";
    echo "   3. Run: php artisan config:clear\n";
    echo "   4. Run: php artisan cache:clear\n";
    echo "   5. Test your application\n\n";
} else {
    echo "ℹ️  No routes were replaced. All routes might be already updated.\n\n";
}

// Juga replace di Controllers
echo "\n⚠️  Don't forget to update routes in Controllers too!\n";
echo "   Check these files:\n";
echo "   - app/Http/Controllers/FakultasController.php\n";
echo "   - app/Http/Controllers/DepartemenController.php\n";
echo "   - app/Http/Controllers/JabatanController.php\n";
echo "   - app/Http/Controllers/KaryawanController.php\n";
echo "   - app/Http/Controllers/ShiftController.php\n";
echo "   - app/Http/Controllers/PresensiController.php\n";
echo "   - app/Http/Controllers/HomeController.php\n\n";