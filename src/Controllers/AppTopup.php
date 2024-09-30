<?php
namespace Hanoivip\Payment\Controllers;

use App\Http\Controllers\Controller;
use Hanoivip\Payment\Services\NewTopupService;
use Illuminate\Http\Request;
/**
 *
 * @author hanoivip
 *
 */
class AppTopup extends Controller
{
    private $service;
    
    public function __construct(NewTopupService $service)
    {
        $this->service = $service;
    }
    
    public function methods(Request $request)
    {
        $client = null;
        if ($request->has('client'))
        {
            $client = $request->input('client');
        }
        $methods = $this->service->getMethods($client);
        $ret = [];
        if (!empty($methods)) {
            foreach ($methods as $method => $cfg) {
                if (!empty($cfg->show))
                    $ret[$method] = $cfg;
            }
        }
        return ['error' => 0, 'message' => '', 'data' => $ret];
    }
}