<?php

namespace App\Http\Livewire\Backups;

use Livewire\Component;
use Livewire\WithFileUploads;

class DatabaseManager extends Component
{
    use WithFileUploads;

    public $backupFile;
    public $notification = null;
    public $notificationType = '';
    public $lastBackupFilename;

    protected function rules()
    {
        return [
            'backupFile' => 'required|file|mimes:bak,sql|max:50000', // 50MB máximo
        ];
    }

    public function render()
    {
        // Devuelve solo la vista del componente, sin layout implicado
        return view('livewire.backups.database-manager');
    }

    public function downloadBackup()
    {
        try {
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host     = config('database.connections.mysql.host');

            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $this->lastBackupFilename = $filename;

            $path = storage_path('app/backups');
            if (! file_exists($path)) {
                mkdir($path, 0777, true);
            } else {
                chmod($path, 0777);
            }

            $fullPath      = $path . '/' . $filename;
            $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';

            $command = escapeshellcmd($mysqldumpPath);
            $args    = [
                '--user='   . escapeshellarg($username),
                '--host='   . escapeshellarg($host),
            ];

            if ($password) {
                $args[] = '--password=' . escapeshellarg($password);
            }

            $args[]      = escapeshellarg($database);
            $fullCommand = $command
                         . ' ' . implode(' ', $args)
                         . ' > ' . escapeshellarg($fullPath)
                         . ' 2>&1';

            exec($fullCommand, $output, $returnVar);

            if ($returnVar !== 0) {
                $errorMessage = implode("\n", $output);
                $this->showNotification("Error al generar respaldo: $errorMessage", 'error');
                return;
            }

            if (! file_exists($fullPath) || filesize($fullPath) === 0) {
                $this->showNotification("El archivo de respaldo no se creó correctamente.", 'error');
                return;
            }

            return response()->download($fullPath)->deleteFileAfterSend(true);
        }
        catch (\Exception $e) {
            $this->showNotification('Error: ' . $e->getMessage(), 'error');
        }
    }

    public function restoreDatabase()
    {
        try {
            $this->validate();

            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host     = config('database.connections.mysql.host');

            $tempPath  = $this->backupFile->getRealPath();
            $mysqlPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysql.exe';

            $command = escapeshellcmd($mysqlPath);
            $args    = [
                '--user=' . escapeshellarg($username),
                '--host=' . escapeshellarg($host),
            ];

            if ($password) {
                $args[] = '--password=' . escapeshellarg($password);
            }

            $args[]      = escapeshellarg($database);
            $fullCommand = $command
                         . ' ' . implode(' ', $args)
                         . ' < ' . escapeshellarg($tempPath)
                         . ' 2>&1';

            exec($fullCommand, $output, $returnVar);

            if ($returnVar !== 0) {
                $errorMessage = implode("\n", $output);
                $this->showNotification("Error al restaurar BD: $errorMessage", 'error');
                return;
            }

            $this->reset(['backupFile']);
            $this->showNotification('Base de datos restaurada con éxito.', 'success');
        }
        catch (\Exception $e) {
            $this->showNotification('Error: ' . $e->getMessage(), 'error');
        }
    }

    private function showNotification($message, $type)
    {
        $this->notification     = $message;
        $this->notificationType = $type;
        $this->dispatch('showNotification');
    }
}
