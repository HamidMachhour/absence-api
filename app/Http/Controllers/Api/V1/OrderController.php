<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Absence;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $page_size = 6)
    {
        try {
            if ($request->filled("page_size"))
                $page_size = $request->input("page_size");

            return Absence::latest()
                ->where('userId', Auth::user()->id)
                ->filter(request(['classeId']))
                ->with('classe')
                ->paginate($page_size);
        } catch (Exception $e) {
            return response()->json(['message' => 'غير متوفر. ' . $e->getMessage()], 404);
        }
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
        //
    }

    /**
     * cancel an absence.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Absence $absence)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }

        if (Auth::user()->id !== $absence->userId) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }

        try {
            DB::transaction(function () use ($absence) {
                $data = ['absenceId' => $absence->id, 'status' => 'CANCELLED'];
                OrderStatus::create($data);
                $absence->update(['updated_at' => now()]);
            });
            // Return a response
            return response()->json(['message' => 'Absence cancelled successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
    }

    /**
     * payment of an absence.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment(Absence $absence)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'غير مسموح، المرجو تسجيل الدخول'], 403);
        }

        if (Auth::user()->id !== $absence->userId) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
        }
        try {
            DB::transaction(function () use ($absence) {
                $data = ['absenceId' => $absence->id, 'status' => 'PAID'];
                OrderStatus::create($data);
                $absence->update(['updated_at' => now()]);
            });
            // Return a response
            return response()->json(['message' => 'Absence paid successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'تم الرفض، لا تمتلك التفويض الكافي للقيام بالعملية'], 403);
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
