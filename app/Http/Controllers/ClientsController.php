<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientsRequest;
use App\Http\Requests\UpdateClientsRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of Client.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        if (! Gate::allows('client_access')) {
//            return abort(401);
//        }

        $clients = Client::all();

        return response($clients);
    }

    /**
     * Store a newly created Client in storage.
     *
     * @param  \App\Http\Requests\StoreClientsRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientsRequest $request)
    {
//        if (! Gate::allows('client_create')) {
//            return abort(401);
//        }
        Client::create($request->all());

        return response(null, 201);
    }

    /**
     * Update Client in storage.
     *
     * @param  \App\Http\Requests\UpdateClientsRequest $request
     * @param Client $client
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientsRequest $request, Client $client)
    {
//        if (! Gate::allows('client_edit')) {
//            return abort(401);
//        }

        $client->update($request->all());

        return response($client);
    }


    /**
     * Display Client.
     *
     * @param Client $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
//        if (! Gate::allows('client_view')) {
//            return abort(401);
//        }
//        $relations = [
//            'appointments' => \App\Appointment::where('client_id', $id)->get(),
//        ];

        $client->load('appointments');

        return response($client);
        //return view('admin.clients.show', compact('client') + $relations);
    }


    /**
     * Remove Client from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        if (! Gate::allows('client_delete')) {
//            return abort(401);
//        }
        $client = Client::findOrFail($id);
        $client->delete();

        return response(null, 204);

    }

    /**
     * Delete all selected Client at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
//        if (! Gate::allows('client_delete')) {
//            return abort(401);
//        }
        if ($request->input('ids')) {
            $entries = Client::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

    public function appointments(Client $client)
    {
        return $client;
    }

}
