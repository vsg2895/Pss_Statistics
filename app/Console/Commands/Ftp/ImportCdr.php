<?php

namespace App\Console\Commands\Ftp;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ImportCdr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cdr {--prevDay}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download cdr files using ftp and import data to local db.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

//        $singleArgument = $this->option('single');
        try {//missing cdr files for 24-01-2022 after 17:00
            $host = config('apiKeys.gcm.host');
            $port = config('apiKeys.gcm.port');
            $username = config('apiKeys.gcm.username');
            $password = config('apiKeys.gcm.password');
            $remoteDir = '/home/gcmcdr/';
//            $remoteDir = '/home/gcmcdr/old/';//for old files
            $localDir = config('filesystems.paths.cdr');
            $prevDayOption = $this->option('prevDay');
            $hoursToTake = $prevDayOption ? 8 : 2;

            $date = Carbon::now()->subHours($hoursToTake)->format('d-m-Y');
            $year = Carbon::now()->subHours($hoursToTake)->format('Y');
            $monthYear = Carbon::now()->subHours($hoursToTake)->format('m-Y');

            $currentDir = $year . '/' . $monthYear . '/' . $date . '/';

            $localDir = $localDir . $currentDir;

            if (!file_exists($localDir)) {
                if (!mkdir($localDir, 0777, true)) {
                    die('Failed to create directories...');
                }
            }

            if (!function_exists("ssh2_connect"))
                die('Function ssh2_connect not found, you cannot use ssh2 here');

            if (!$connection = ssh2_connect($host, $port))
                die('Unable to connect');

            if (!ssh2_auth_password($connection, $username, $password))
                die('Unable to authenticate.');

            if (!$stream = ssh2_sftp($connection))
                die('Unable to create a stream.');

            if (!$dir = opendir("ssh2.sftp://{$stream}{$remoteDir}"))
                die('Could not open the directory');

            $_2hourBehindZ = Carbon::now()->subHours($hoursToTake)->format('Ymd');
            $files = [];
//            dd($_2hourBehindZ);
            while (false !== ($file = readdir($dir))) {
                if ($file == "." || $file == "..")
                    continue;
                if (strpos($file, $_2hourBehindZ)) {
                    $files[] = $file;
                }
            }
//            dd($files);
            foreach ($files as $file)// gcmcdr-2022011900.txt
            {
                $_2hourBehind = Carbon::now()->subHours($hoursToTake)->format('YmdH');
//                if (!strpos($file, $_2hourBehind) || (!$prevDayOption && strlen($file) !== 19)) {
                if (!strpos($file, $_2hourBehind)  && ($prevDayOption && strlen($file) !== 19)) {
                    $messageZ = "File is not 2 hour behind, already imported $file\n";
                    echo $messageZ;
                    continue;
                }

                echo "Copying file: $file\n";
                $path = "{$remoteDir}/{$file}";
                if (!$remote = @fopen("ssh2.sftp://{$stream}/{$path}", 'r')) {
                    $messageZ = "Unable to open remote file: $file\n";
                    echo $messageZ;
                    Log::info($messageZ);
                    continue;
                }

                if (!$local = @fopen($localDir . $file, 'w')) {
                    $messageZ = "Unable to create local file: $file\n";
                    echo $messageZ;
                    Log::info($messageZ);
                    continue;
                }

                $read = 0;
                $filesize = filesize("ssh2.sftp://{$stream}/{$path}");
                while ($read < $filesize && ($buffer = fread($remote, $filesize - $read))) {
                    $read += strlen($buffer);
                    if (fwrite($local, $buffer) === FALSE) {
                        $messageZ = "Unable to write to local file: $file\n";
                        echo $messageZ;
                        Log::info($messageZ);
                        break;
                    }
                }
                fclose($local);
                fclose($remote);
            }

            $this->info('Cdr files imported successfully.');
            Log::info('Cdr files imported successfully.');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            $messageText = 'import:cdr failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine();
            Log::error($messageText);
            Mail::send([], [], function ($message) use ($messageText) {
                $message->to(config('mail.mail_dev'))->subject('insert:cdr failed')->setBody($messageText);
            });
        }

    }
}
