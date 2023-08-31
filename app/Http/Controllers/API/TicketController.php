<?php

namespace App\Http\Controllers\API;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\API\BaseController as BaseController;

class TicketController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::all();
        return $this->sendResponse(TicketResource::collection($tickets), 'Tickets retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'AgentName' => 'required',
            'SubjectCase' => 'required',
            'CallerName' => 'required',
            'CallerEmail' => 'required|email',
            'Status' => 'required',
            'Priority' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $ticket = Ticket::create($validator->validated());
        return $this->sendResponse(new TicketResource($ticket), 'Ticket Created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Ticket not found.');
        }
        return $this->sendResponse(new TicketResource($ticket), 'Ticket retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validator = Validator::make($request->all(), [
            'AgentName' => 'required',
            'SubjectCase' => 'required',
            'CallerName' => 'required',
            'CallerEmail' => 'required|email',
            'Status' => 'required',
            'Priority' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $ticket->update($validator->validated());
        return $this->sendResponse(new TicketResource($ticket), 'Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        if (!$ticket->exists) {
            return $this->sendError('Ticket not found.');
        }
        $ticket->delete();
        return $this->sendResponse([], 'Ticket deleted successfully.');
    }
}