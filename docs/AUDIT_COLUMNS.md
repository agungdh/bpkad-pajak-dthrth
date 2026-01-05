# Audit Columns Implementation

## Overview
Implementasi audit columns untuk tracking lengkap siapa yang membuat, mengupdate, dan menghapus data pada setiap model. Menggunakan **epoch timestamp** (Unix timestamp dalam bentuk integer) untuk semua kolom waktu.

## Audit Columns
Setiap model yang menggunakan audit columns akan memiliki:
- `created_at` - **Epoch timestamp** kapan data dibuat (unsignedBigInteger)
- `updated_at` - **Epoch timestamp** kapan data terakhir diupdate (unsignedBigInteger)
- `deleted_at` - **Epoch timestamp** kapan data dihapus (unsignedBigInteger)
- `created_by` - User ID siapa yang membuat data (unsignedBigInteger, tanpa foreign key)
- `updated_by` - User ID siapa yang terakhir mengupdate data (unsignedBigInteger, tanpa foreign key)
- `deleted_by` - User ID siapa yang menghapus data (unsignedBigInteger, tanpa foreign key)

> **Note**: Semua timestamp menggunakan epoch/Unix timestamp (integer), bukan datetime. Trait `HasAuditColumns` akan otomatis handle epoch timestamps.

## Cara Implementasi ke Model Lain

### 1. Buat Migration untuk Menambahkan Audit Columns

```bash
php artisan make:migration add_audit_columns_to_[table_name]_table --table=[table_name]
```

Contoh isi migration:

```php
public function up(): void
{
    Schema::table('[table_name]', function (Blueprint $table) {
        // Drop existing timestamps if exist (for tables with default Laravel timestamps)
        $table->dropTimestamps();

        // Add epoch-based timestamps
        $table->unsignedBigInteger('created_at')->nullable()->after('[last_column]');
        $table->unsignedBigInteger('updated_at')->nullable()->after('created_at');

        // Add audit columns without foreign keys
        $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
        $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
        $table->unsignedBigInteger('deleted_at')->nullable()->after('updated_by');
        $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
    });
}

public function down(): void
{
    Schema::table('[table_name]', function (Blueprint $table) {
        // Drop epoch-based columns
        $table->dropColumn(['created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at', 'deleted_by']);

        // Restore original timestamps if needed
        $table->timestamps();
    });
}
```

### 2. Update Model

Cukup tambahkan trait `HasAuditColumns` saja (SoftDeletes sudah include di dalamnya):

```php
<?php

namespace App\Models;

use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YourModel extends Model
{
    use HasAuditColumns, HasFactory;
    
    protected $guarded = ['id'];
    
    // Trait HasAuditColumns sudah include:
    // - SoftDeletes functionality
    // - Automatic epoch timestamps handling
    // - Audit columns tracking (created_by, updated_by, deleted_by)
    
    // ... rest of your model code
}
```

### 3. Run Migration

```bash
php artisan migrate
```

## Fitur Trait HasAuditColumns

### Automatic Tracking
Trait akan otomatis mengisi:
- `created_at`, `updated_at`, `created_by` dan `updated_by` saat data dibuat
- `updated_at` dan `updated_by` saat data diupdate
- `deleted_at` dan `deleted_by` saat data di-soft delete

> **Important**: Trait ini akan override Laravel's automatic timestamps behavior dan menggunakan epoch timestamps (integer) instead.

### Relationships
Trait menyediakan 3 relationship methods:
- `creator()` - User yang membuat data
- `updater()` - User yang terakhir mengupdate data
- `deleter()` - User yang menghapus data

Contoh penggunaan:
```php
$user = User::find(1);
echo $user->creator->name; // Nama user yang membuat
echo $user->updater->name; // Nama user yang terakhir update
echo $user->deleter->name; // Nama user yang menghapus (jika sudah dihapus)
```

## Tentang Epoch Timestamp

### Kenapa Epoch?
- **Lebih efisien**: Integer lebih kecil dari string datetime
- **Universal**: Tidak ada timezone confusion
- **Mudah dimanipulasi**: Operasi matematika langsung pada integer
- **API friendly**: JSON serialization lebih clean

### Cara Kerja
Trait `HasAuditColumns` menggunakan model observers untuk:
1. **Creating**: Set `created_at`, `updated_at`, `created_by`, `updated_by` dengan `time()` (epoch)
2. **Updating**: Set `updated_at` dan `updated_by` dengan `time()` (epoch)
3. **Deleting** (soft delete): Set `deleted_at` dan `deleted_by` dengan `time()` (epoch)

Contoh nilai epoch timestamp:
```php
// Database menyimpan: 1736050773 (integer)
$user->created_at; // 1736050773 (integer)

// Jika perlu convert ke Carbon object untuk formatting:
$createdAt = \Carbon\Carbon::createFromTimestamp($user->created_at);
$createdAt->format('Y-m-d H:i:s'); // "2026-01-05 10:59:33"
$createdAt->diffForHumans(); // "5 minutes ago"
```

## Notes
- Audit columns hanya terisi jika ada user yang sedang authenticated (`Auth::check()`)
- Jika tidak ada user yang login, kolom `created_by`, `updated_by`, `deleted_by` akan tetap `null`
- Timestamps (`created_at`, `updated_at`, `deleted_at`) akan tetap terisi dengan epoch timestamp
- `deleted_by` hanya terisi pada soft delete, tidak pada force delete
- **Tidak menggunakan foreign key** sehingga lebih fleksibel dan tidak ada cascade issue
- Semua timestamp disimpan sebagai **epoch/Unix timestamp** (integer)
- Timestamps akan di-set oleh trait, **bukan** oleh Eloquent's automatic timestamps

## Testing
Contoh test sudah tersedia di `tests/Feature/UserAuditColumnsTest.php` yang bisa dijadikan referensi untuk model lain.
