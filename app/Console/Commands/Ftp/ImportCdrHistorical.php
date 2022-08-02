<?php

namespace App\Console\Commands\Ftp;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ImportCdrHistorical extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:historical-cdr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download historical cdr files using ftp and import data to local db.';

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

        try {//missing cdr files for 24-01-2022 after 17:00
            $host = config('apiKeys.gcm.host');
            $port = config('apiKeys.gcm.port');
            $username = config('apiKeys.gcm.username');
            $password = config('apiKeys.gcm.password');
//            $remoteDir = '/home/gcmcdr/';
            $remoteDir = '/home/gcmcdr/old/';//for old files
//            dd($remoteDir);
            $localBaseDir = config('filesystems.paths.cdr');

//            if (!function_exists("ssh2_connect"))
//                die('Function ssh2_connect not found, you cannot use ssh2 here');

            if (!$connection = ssh2_connect($host, $port))
                die('Unable to connect');

            if (!ssh2_auth_password($connection, $username, $password))
                die('Unable to authenticate.');

            if (!$stream = ssh2_sftp($connection))
                die('Unable to create a stream.');

            if (!$dir = opendir("ssh2.sftp://{$stream}{$remoteDir}"))
                die('Could not open the directory');

            $files = [];
            while (false !== ($file = readdir($dir))) {
                if ($file == "." || $file == "..")
                    continue;

                $fileDate = explode('.', explode('-', $file)[1])[0];

//                dump($fileDate);
                if((int)$fileDate >= 2022012400 || (strlen($fileDate) == 8 && (int)$fileDate >= 20220124)) {
                    $files[] = $file;
                }

//                $result = $this->getPathParams($file, true);
////                dd(5 >= 01);
////                dd($result,'ddd');
////                if ($result['yearValue'] == 2022)
////                    dd(readdir($dir), $result['month'], $result['day']);
////                dump($result['yearValue'], $result['month'], $result['day']);
////                if ($result['yearValue'] >= 2022 && $result['month'] >= 1 && $result['day'] >= 24) {
//                if ($result['yearValue'] == 2022 && $result['month'] == 1) {
//                    dump($result['day']);
//                    $files[] = $file;
//                }
            }
            foreach ($files as $file)// gcmcdr-2022011900.txt
            {
//                dd($file);
                $date = Carbon::now()->subHours(2)->format('d-m-Y');
                $result = $this->getPathParams($file);
                $currentDir = $result['yearValue'] . '/' . $result['month'] . '/' . $result['day'] . '/';

                $localDir = $localBaseDir . $currentDir;

                if (!file_exists($localDir)) {
                    if (!mkdir($localDir, 0777, true)) {
                        die('Failed to create directories...');
                    }
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


    private function getPathParams($file, $dateFormat = false)
    {
        $arrayDateLength = [
            'year' => 4,
            'month' => 2,
            'day' => 2
        ];
        $countString = strlen('gcmcdr-');
        $dropLength = strlen('-');
        $dateName = substr($file, $countString);
        $yearValue = substr($dateName, 0, $arrayDateLength['year']);
        $monthValue = substr($dateName, $arrayDateLength['year'], $arrayDateLength['month']);
        $dayValue = substr($dateName, $arrayDateLength['year'] + $arrayDateLength['month'], $arrayDateLength['day']);
        $month = $monthValue . '-' . $yearValue;
        $day = $dayValue . '-' . $monthValue . '-' . $yearValue;

        return [
            'dateName' => $dateName,
            'yearValue' => $dateFormat ? (int)$yearValue : $yearValue,
            'month' => $dateFormat ? (int)substr($month, $dropLength)[0] : $month,
            'day' => $dateFormat ? (int)substr($day, $dropLength)[0] : $day,
        ];
    }
}
