@echo off
REM Menghapus file versi lama (sistem servis aset) dari proyek Laravel Anda.
REM Pemakaian:  hapus-file-lama.bat C:\laragon\www\proyek-pertama-rpl
set P=%~1
if "%P%"=="" (
  echo Pemakaian: hapus-file-lama.bat C:\laragon\www\proyek-pertama-rpl
  exit /b 1
)
del /q "%P%\app\Models\Asset.php" "%P%\app\Models\Category.php" "%P%\app\Models\Maintenance.php" 2>nul
del /q "%P%\app\Http\Controllers\AssetController.php" "%P%\app\Http\Controllers\CategoryController.php" "%P%\app\Http\Controllers\MaintenanceController.php" 2>nul
rmdir /s /q "%P%\resources\views\assets" 2>nul
rmdir /s /q "%P%\resources\views\categories" 2>nul
rmdir /s /q "%P%\resources\views\maintenances" 2>nul
del /q "%P%\database\migrations\2025_01_01_*" "%P%\database\migrations\2025_01_02_*" 2>nul
echo File lama sudah dihapus.
