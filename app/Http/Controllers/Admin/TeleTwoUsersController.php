<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\TeleTwoApiInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeleTwoRequest;
use App\Models\TeleTwoUser;
use App\Services\Insert\TeleTwoActionsService;
use Illuminate\Http\Request;

class TeleTwoUsersController extends Controller
{
    private TeleTwoActionsService $ttas;
    private TeleTwoApiInterface $teleTwoApi;


    public function __construct(TeleTwoActionsService $ttas, TeleTwoApiInterface $teleTwoApi)
    {
        $this->ttas = $ttas;
        $this->teleTwoApi = $teleTwoApi;

    }

    public function index()
    {
        $teleTwoUsers = TeleTwoUser::all();

        return view('pages.admin.integrations.index', ['teleTwoUsers' => $teleTwoUsers]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(TeleTwoUser $TeleTwoUser)
    {

    }

    public function showMore(TeleTwoRequest $request)
    {
        $response = $this->ttas->getMore($request->id);

        return response()->json(['res' => $response->body()]);
    }

    public function edit(TeleTwoUser $TeleTwoUser)
    {
        //
    }

    public function update(Request $request, TeleTwoUser $TeleTwoUser)
    {
        //
    }

    public function destroy(TeleTwoUser $TeleTwoUser)
    {
        //
    }

    public function insertTeleTwoUsers()
    {
        //todo 1 add Info button like in users page,
        // show green yes or red No
        // presence->activity->available https://tele2vaxel.se/api/contacts/list/soderbergsbil.se/svarsservice/frank.anebrand@soderbergsbil.se/available?t=5938.VDo3NTg0NTVkZmRlYWFmNGIw&maxResults
        //todo 2 remove rows if they don't exist in api data ($users)
        $this->ttas->insertAll();
        return redirect()->route('admin.integrations.index')->withSuccess(__('Tele Two Users Updated Successfully'));
    }
}
