@echo off
REM Pemakaian: setup.bat nama-folder-proyek
set NAMA=%1
if "%NAMA%"=="" set NAMA=servis-aset
call composer create-project laravel/laravel %NAMA%
xcopy /E /I /Y app %NAMA%\app
xcopy /E /I /Y database %NAMA%\database
xcopy /E /I /Y routes %NAMA%\routes
xcopy /E /I /Y resources %NAMA%\resources
cd %NAMA%
echo APP_TIMEZONE=Asia/Jakarta>> .env
php artisan migrate --seed --force
echo Selesai. Jalankan: php artisan serve
