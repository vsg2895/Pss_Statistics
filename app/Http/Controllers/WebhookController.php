<?php

namespace App\Http\Controllers;

use App\Services\Eazy\EazyChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    private EazyChatService $eazyChatService;

    public function __construct(EazyChatService $eazyChatService)
    {

        $this->eazyChatService = $eazyChatService;
    }

    public function saveEazyChats(Request $request)
    {
        //{"agent":"sara.adle@personligtsvar.se","companyId":"4171","time":"2022-05-04T08:17:44.151265Z"}
        try {
            $this->eazyChatService->saveChats($request->all());

            Log::info('webhook:eazyChat got chat. data: ' . json_encode($request->all()));

            Log::info('webhook:eazyChat success');

            return response()->json([
                'success' => true,
                'message' => "Success",
            ]);
        } catch (\Exception $exception) {
            Log::info('webhook:eazyChat failed' . ' message: ' . $exception->getMessage()
                . ' line: ' . $exception->getLine());

            return response()->json([
                'success' => $exception->getMessage(),
                'message' => "Something went wrong",
                'data' => $request->all(),
            ], 500);
        }
    }

    public function test(Request $request)
    {
        Log::info('webhook data tes: ' . json_encode($request->all()));

        return response()->json([
            'success' => true,
            'message' => "Success",
            'data' => $request->all(),
        ]);
    }
}
