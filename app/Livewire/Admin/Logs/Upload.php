<?php

namespace App\Livewire\Admin\Logs;

use App\Models\Log;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Upload extends Component
{
    public $uploaded = false;

    public $link  = 'test';

    public function uploadLogs(){
        $data = '';

        $data .= 'Version: ' . config('app.version') . "\n";	
        $data .= 'PHP Version: ' . phpversion() . "\n";
        $data .= 'Server OS: ' . PHP_OS . "\n";

        $data .= "\n";

        foreach(Log::orderBy('id', 'desc')->get() as $log){
            if (strlen($data) > 1000000 || strlen($data . $log->created_at . ' - ' . $log->type . ' - ' . $log->message . "\n" . $log->data . "\n" . "\n") > 1000000) {
               break;
            }
            $data .= $log->created_at . ' - ' . $log->type . ' - ' . $log->message . "\n";
            $data .= $log->data . "\n";
            $data .= "\n";
        }

        $this->uploaded = true;
    
        $this->link = Http::post('https://logs.paymenter.org/api/upload-logs', [
            'data' => $data
        ])->json()['url'];
        
    }


    public function render()
    {
        return view('livewire.admin.logs.upload');
    }
}
