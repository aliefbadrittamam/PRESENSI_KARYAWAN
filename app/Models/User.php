<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'address',
        'photo',
        'barcode_token', // ✅ Tambahkan agar bisa di-assign manual
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is HRD
     */
    public function isHrd(): bool
    {
        return $this->role === 'hrd';
    }

    /**
     * Check if user is supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the user's photo URL
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/photos/' . $this->photo);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * ✅ ENHANCEMENT: Generate barcode_token dengan retry mechanism
     * Boot event untuk auto-generate saat creating user
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            // Hanya generate jika barcode_token masih kosong
            if (empty($user->barcode_token)) {
                try {
                    $user->barcode_token = self::generateUniqueBarcode();
                } catch (\Exception $e) {
                    Log::error('Failed to generate unique barcode: ' . $e->getMessage());
                    // Jika gagal, set default UUID (let database constraint handle)
                    $user->barcode_token = Str::uuid()->toString();
                }
            }
        });

        // ✅ ENHANCEMENT: Retry jika terjadi duplicate saat saving
        static::saving(function ($user) {
            // Validasi barcode_token tidak kosong
            if (empty($user->barcode_token)) {
                $user->barcode_token = Str::uuid()->toString();
            }
        });
    }

    /**
     * ✅ ENHANCEMENT: Generate unique barcode dengan retry mechanism
     * 
     * @param int $maxRetries Maximum retry attempts
     * @return string Unique barcode token
     * @throws \Exception if failed after max retries
     */
    private static function generateUniqueBarcode(int $maxRetries = 5): string
    {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            // Generate UUID
            $token = Str::uuid()->toString();
            
            // Check if exists
            $exists = self::where('barcode_token', $token)->exists();
            
            if (!$exists) {
                Log::info("Generated unique barcode on attempt {$attempt}");
                return $token;
            }
            
            // Log collision (sangat jarang terjadi dengan UUID)
            Log::warning("Barcode collision on attempt {$attempt}: {$token}");
            
            // Sleep sebelum retry (100ms * attempt)
            if ($attempt < $maxRetries) {
                usleep(100000 * $attempt);
            }
        }
        
        // Jika semua retry gagal, throw exception
        throw new \Exception("Failed to generate unique barcode after {$maxRetries} attempts");
    }

    /**
     * ✅ ENHANCEMENT: Public method untuk regenerate barcode
     * Digunakan di ProfileController untuk regenerate QR Code
     * 
     * @return string New unique barcode
     * @throws \Exception if failed to generate
     */
    public static function generateNewBarcode(): string
    {
        return self::generateUniqueBarcode();
    }

    /**
     * ✅ ENHANCEMENT: Safe method untuk update barcode dengan retry
     * 
     * @return bool Success status
     */
    public function regenerateBarcode(): bool
    {
        try {
            $newToken = self::generateUniqueBarcode();
            
            // Update dengan query builder untuk ensure atomic operation
            $affected = self::where('id', $this->id)
                ->update(['barcode_token' => $newToken]);
            
            if ($affected > 0) {
                $this->barcode_token = $newToken;
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to regenerate barcode for user {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ ENHANCEMENT: Validate barcode uniqueness
     * 
     * @param string $token Token to validate
     * @param int|null $excludeUserId User ID to exclude from check
     * @return bool True if unique
     */
    public static function isBarcodeUnique(string $token, ?int $excludeUserId = null): bool
    {
        $query = self::where('barcode_token', $token);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return !$query->exists();
    }
}