<?php

namespace App\Console\Commands;

use App\Libraries\WxPay;
use App\Modules\System\TxConfig;
use Illuminate\Console\Command;

class Transfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $transfers = \App\Modules\User\Transfer::where('state','=',1)->get();
        $config = TxConfig::first();
        $wxpay = new WxPay($config->app_id,$config->mch_id,$config->api_key);
        if (!empty($transfers)){
            $path = base_path().'/public/';
            foreach ($transfers as $transfer){
                $data = $wxpay->transfer($transfer->number,$transfer->open_id,$transfer->amount,$transfer->desc,'39.104.98.40',$path.$config->ssl_cert,$path.$config->ssl_key);
//                $data = $this->xmlToArray($data);
                dump($data);
                if ($data['result_code']=='SUCCESS'){
                    $transfer->state = 2;
                    $transfer->save();
                }else{
                }
            }
        }
    }
    public function xmlToArray($xml)
    {
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }
}
