<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\JsonResponse;
use App\Models\Classe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Api\V1\ClasseController as V1ClasseController;
use ArPHP\I18N\Arabic;

class ClasseController extends Controller
{
    //Destroy all classes
    public function destroyAll(Request $request, V1ClasseController $controller)
    {
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('manager')) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $response = $controller->destroyAll($request);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('classes.index')->with('message', $message);
    }

    //print student cards
    public function printCards(Classe $classe)
    {
        $students = $classe->students;

        $students = $classe->students->map(function ($student) {
            $Arabic = new Arabic();
            $student->fullName = $Arabic->utf8Glyphs($student->user->fullName());
            $student->al9ism = $Arabic->utf8Glyphs($student->classe->classeName());
            $student->ara9m =  $student->num;

            $student->qrCodeBase64 = imageToBase64($student->qrCodeImagePath());
            $student->studentImageBase64 = imageToBase64($student->studentImage->uri ?? public_path('assets/images/no-image.png'));
            return $student;
        });
        $pdf = Pdf::loadView('students.printCards', [
            'students' => $students,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('isFontSubsettingEnabled', true); // important for Arabic
        // Set filename
        $filename = 'cards-' . $classe->classeName() . '-' . time() . '.pdf';

        // Return the PDF for download
        return $pdf->download($filename);
    }
    // Show all classes
    public function index(Request $request, V1ClasseController $v1Controller)
    {
        $response = $v1Controller->index($request);
        if ($response instanceof JsonResponse) {
            $status = $response->getStatusCode();
            $data = $response->getData(); // Get the underlying data object from the JSON response

            // Test on response status for errors
            if ($status === 403 || $status === 404) {
                $message = $data->message ?? __('vocab.not_authorized');
                return redirect()->route('error', ['message' => $message]);
            }
        }

        $params = $this->prepareFilterParams($request, $response);
        $count = is_array($response) ? count($response) : $response->total();
        return view('classes.index', [
            'classes' => $response,
            'params' => $params,
            'totalCount' => $count
        ]);
    }

    // Show all authenticated user's classes
    public function manage(Request $request, V1ClasseController $v1Controller)
    {

        $response = $v1Controller->show($request);

        if ($response instanceof JsonResponse) {
            $status = $response->getStatusCode();
            $data = $response->getData(); // Get the underlying data object from the JSON response

            // Test on response status for errors
            if ($status === 403 || $status === 404) {
                $message = $data->message ?? __('vocab.not_authorized');
                return redirect()->route('error', ['message' => $message]);
            }
        }
        $params = $this->prepareFilterParams($request, $response);
        $count = is_array($response) ? count($response) : $response->total();
        return view('classes.manage', [
            'classes' => $response,
            'params' => $params,
            'totalCount' => $count
        ]);
    }
    public function prepareFilterParams(Request $request, $response)
    {

        $params = [];
        if (!$response || count($response) == 0)
            return $params;

        if ($request->filled('id')) {
            $params['classeId'] = __('vocab.class') . ':' . $response->first()->classeName();
        }
        if ($request->filled('userId')) {
            $params['userId'] = __('vocab.manager') . ':' . $response->first()->user->fullName();
        }
        if ($request->filled('tag')) {
            $params['tag'] = __('vocab.tag') . ':' . $request->tag;
        }
        if ($request->filled('search')) {
            $params['search'] = __('vocab.search') . ':' . $request->search;
        }

        return $params;
    }
    //Show single classe
    public function show(Classe $classe)
    {
        return view('classes.show', [
            'classe' => $classe
        ]);
    }

    // Show Create Form
    public function create()
    {
        return view('classes.create');
    }

    // Store class Data
    public function store(Request $request, V1ClasseController $controller)
    {
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_success');
            return redirect()->route('classes.manage')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
            return back()->withErrors(['message' => $message]);;
        }
    }

    // Show Edit Form
    public function edit(Classe $classe)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $classe->userId) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        return view('classes.edit', ['classe' => $classe, 'addresses' => Auth::user()->addresses]);
    }

    // Update store Data
    public function update(Request $request, Classe $classe)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $classe->userId) {
            return redirect()->route('error', ['message' =>  __('vocab.not_authorized')]);
        }
        $controller = new V1ClasseController();
        $response = $controller->update($request, $classe);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : __('vocab.update_success');
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.update_failure') . ' ' . __('vocab.try_again');
        }
        return redirect()->route('classes.edit', ['classe' => $classe])->with('message', $message);
    }

    // Delete store
    public function destroy(Classe $classe)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $classe->userId) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $controller = new V1ClasseController();
        $response = $controller->destroy($classe);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('classes.manage')->with('message', $message);
    }


    public function export(Classe $classe)
    {
        $controller = new V1ClasseController();
        $response = $controller->export($classe);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 200) {
            return $response;
        }

        $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure');

        return redirect()->route('classes.manage')->with('message', $message);
    }


    public function importForm(Classe $classe)
    {


        return view('classes.import', ['classe' => $classe]);
    }

    // New method to show import form for classes
    public function importClassesForm()
    {
        return view('classes.import_classes');
    }

    public function import(Request $request, Classe $classe, V1ClasseController $controller)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $classe->userId) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }

        $response = $controller->import($request, $classe);
        $status_code = $response->getStatusCode();

        // Check if the response from V1SeanceController is a successful download response
        if ($status_code === 200 && $response->headers->has('Content-Disposition')) {
            return $response; // Directly return the file response to the browser
        }

        // If it's not a download response (e.g., an error response),
        // then process it to get the message and redirect.
        $responseContent = $response->content();
        $data = json_decode($responseContent, true);

        if ($status_code === 200) {
            //  return $response;
            $message = isset($headers['added']) ? $headers['added'] . __('vocab.students_added') : __('vocab.import_success');
            return redirect()->route('students.manage', ['classe' => $classe->id])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure');
            return redirect()->route('students.manage', ['classe' => $classe->id])->with('message', $message);
        }
    }

    public function exportClasses(Request $request, V1ClasseController $controller)
    {
        $response = $controller->exportClasses($request);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 200) {
            return $response;
        }

        $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure');;

        return redirect()->route('classes.manage')->with('message', $message);
    }


    public function importClasses(Request $request, V1ClasseController $controller)
    {
        $response = $controller->importClasses($request);
        $status_code = $response->getStatusCode();

        // Check if the response from V1SeanceController is a successful download response
        if ($status_code === 200 && $response->headers->has('Content-Disposition')) {
            return $response; // Directly return the file response to the browser
        }

        // If it's not a download response (e.g., an error response),
        // then process it to get the message and redirect.
        $responseContent = $response->content();
        $data = json_decode($responseContent, true);
        if ($status_code === 200) {
            //  return $response;
            $message = isset($data['added']) ? $data['added'] . __('vocab.classes_added') : __('vocab.import_success');
            return redirect()->route('classes.manage')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure');;
            return redirect()->route('classes.manage')->with('message', $message);
        }
    }
}
