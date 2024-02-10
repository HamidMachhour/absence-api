<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\V1\OrderController as V1OrderController;
use App\Http\Controllers\Controller;
use App\Models\Absence;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $v1Controller = new V1OrderController();
        return view('absences.index', [
            'absences' => $v1Controller->index($request)
        ]);
    }

    /**
     * cancel an absence.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Absence $absence)
    {
        $controller = new V1OrderController();

        $response = $controller->cancel($absence);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code !== 200) {
            $message = isset($data['message']) ? $data['message'] : 'Unauthorized, you cannot cancel this absence!';
            return redirect()->route('error.route', ['message' => $message]);
        }
        $message = isset($data['message']) ? $data['message'] : 'Absence cancelled!';

        return redirect()->route("absences.index")->with('message', $message);
    }

    /**
     * payment process of an absence.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment(Absence $absence)
    {
        $controller = new V1OrderController();

        $response = $controller->payment($absence);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code !== 200) {
            $message = isset($data['message']) ? $data['message'] : 'Payment failed, go back and try again!';
            return redirect()->route('error.route', ['message' => $message]);
        }
        $message = isset($data['message']) ? $data['message'] : 'Payment successful!';

        return redirect()->route("absences.index")->with('message', $message);

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function show(Absence $absence)
    {
        if (auth()->user()->id === $absence->userId) {
            return view('absences.show', [
                'absence' => $absence
            ]);
        } else {
            return redirect()->route('home.route');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function edit(Absence $absence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Absence $absence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Absence $absence)
    {
        //
    }
}
