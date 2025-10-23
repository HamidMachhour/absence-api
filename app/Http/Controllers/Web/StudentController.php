<?php

namespace App\Http\Controllers\Web;

use Exception;
use App\Models\Classe;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\StudentController as V1StudentController;
use Illuminate\Http\JsonResponse;
use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{

    //print student cards
    public function printCard(Student $student)
    {
        $Arabic = new Arabic();
        $student->fullName = $Arabic->utf8Glyphs($student->user->fullName());
        $student->al9ism = $Arabic->utf8Glyphs($student->classe->classeName());
        $student->ara9m = $student->num;

        $student->qrCodeBase64 = imageToBase64($student->qrCodeImagePath());
        $student->studentImageBase64 = imageToBase64($student->studentImage->uri ?? public_path('assets/images/no-image.png'));
        $pdf = Pdf::loadView('students.printCard', [
            'student' => $student,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('isFontSubsettingEnabled', true); // important for Arabic
        // Set filename
        $filename = 'Student-' . $student->id . '-' . time() . '.pdf';

        // Return the PDF for download
        return $pdf->download($filename);
    }

    //print student cards
    public function printCards(Classe $classe)
    {
        $students = $classe->students;

        $students = $classe->students->map(function ($student) {
            $Arabic = new Arabic();
            $student->fullName = $Arabic->utf8Glyphs($student->user->fullName());
            $student->al9ism = $Arabic->utf8Glyphs($student->classe->classeName());
            $student->ara9m = $student->num;

            Log::info('Student ID: ' . $student->id . ', QR Code Path: ' . $student->qrCodeImagePath());

            $student->qrCodeBase64 = imageToBase64($student->qrCodeImagePath());
            $student->studentImageBase64 = imageToBase64($student->studentImage->uri ?? public_path('assets/images/no-image.png'));
            return $student;
        });
        $pdf = Pdf::loadView('students.printCards', [
            'students' => $students,
            'classe' => $classe,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('isFontSubsettingEnabled', true) // important for Arabic
            // 👇 Set Margins Here (in millimeters 'mm')
            ->setOption('margin-top', 5)
            ->setOption('margin-right', 5)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 5);
        // Set filename
        $filename = 'cards-' . $classe->classeName() . '-' . time() . '.pdf';

        // Return the PDF for download
        return $pdf->download($filename);
    }


    public function getStudents(Request $request, Classe $classe, V1StudentController $v1Controller)
    {
        return $v1Controller->getStudents($request, $classe);
    }
    // Show all students
    public function index(Request $request, V1StudentController $v1Controller)
    {
        try {
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


            return view('students.index', [
                'students' => $response,
                'params' => $params
            ]);
        } catch (Exception $e) {
            return view('error', ["message" => __('vocab.empty')  . $e->getMessage()]);
        }
    }


    public function manage(Request $request, Classe $classe)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $classe->userId) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $v1Controller = new V1StudentController();

        $response = $v1Controller->show($request, $classe);

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
        $params['classeId'] = __('vocab.class') . ' ' . $classe->classeName();

        return view('students.manage', [
            'classe' => $classe,
            'students' => $response,
            'params' => $params
        ]);
    }


    public function cards(Request $request, Classe $classe, V1StudentController $v1Controller)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $classe->userId) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }

        $response = $v1Controller->show($request, $classe);

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
        $params['classeId'] = __('vocab.class') . ' ' . $classe->classeName();

        return view('students.cards', [
            'classe' => $classe,
            'students' => $response,
            'params' => $params
        ]);
    }
    public function prepareFilterParams(Request $request, $response)
    {

        $params = [];
        if (!$response || count($response) == 0)
            return $params;

        if ($request->filled('orderBy')) {
            $params['orderBy'] = 'مع الترتيب';
        }
        if ($request->filled('classeId')) {
            $params['classeId'] = __('vocab.class') . ' ' . $response->first()->classe->classeName();
        }
        if ($request->filled('userId')) {
            $params['userId'] = __('vocab.student') . $response->first()->user->fullName();
        }
        if ($request->filled('minPlage')) {
            $params['minPlage'] = 'اكثر من ' . $request->minPlage . ' ساعة غياب';
        }
        if ($request->filled('maxPlage')) {
            $params['maxPlage'] = 'اقل من ' . $request->maxPlage . ' ساعة غياب';
        }

        if ($request->filled('minAbsences')) {
            $params['minAbsences'] = 'اكثر من ' . $request->minAbsences . ' حالة غياب';
        }
        if ($request->filled('maxAbsences')) {
            $params['maxAbsences'] = 'اقل من ' . $request->maxAbsences . ' حالة غياب';
        }
        if ($request->filled('search')) {
            $params['search'] = __('vocab.search') . ' ' . $request->search;
        }

        return $params;
    }
    //Show single student
    public function show(Student $student)
    {
        return view('students.show', [
            'student' => $student
        ]);
    }

    // Store student Data
    public function store(Request $request, V1StudentController $controller)
    {

        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : 'Student created!';
            return redirect()->route('students.manage', ['classe' => $request->get('classeId')])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.action_failure') . ' ' . __('vocab.try_again');
            return back()->withErrors(['message' => $message]);;
        }
    }

    // Show Edit Form
    public function edit(Student $student)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $student->classe->userId) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $classes = Classe::where('userId', $student->classe->userId)->get();
        return view('students.edit', ['student' => $student, 'classes' => $classes]);
    }

    public function card(Student $student)
    {
        if (
            !Auth::user()->hasRole('admin')
            && Auth::user()->id !== $student->classe->userId
            && Auth::user()->id !== $student->userId
        ) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        return view('students.card', ['student' => $student]);
    }
    // Update student Data
    public function update(Request $request, Student $student)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $student->classe->userId) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }
        $controller = new V1StudentController();
        $response = $controller->update($request, $student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : 'Student updated successfully';
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.update_failure') . ' ' . __('vocab.try_again');
        }
        return redirect()->route('students.edit', ['student' => $student])->with('message', $message);
    }

    // Delete student
    public function destroy(Student $student)
    {
        if (
            !Auth::user()->hasRole('admin') &&
            !(Auth::user()->id === $student->classe->userId)
        ) {
            return redirect()->route('home')->with('message',  __('vocab.not_authorized'));
        }

        $controller = new V1StudentController();
        $response = $controller->destroy($student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code == 200) {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_success');
        } else {
            $message = isset($data['message']) ? $data['message'] : __('vocab.delete_failed');
        }
        return redirect()->route('students.manage', ['classe' => $student->classeId])->with('message', $message);
    }

    // Update student Data
    public function copy(Request $request, Student $student)
    {

        $controller = new V1StudentController();
        $response = $controller->copy($request, $student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 401) {
            $message = isset($data['message']) ? $data['message'] : 'You must set a default store!';
            return redirect()->route('classes.manage')->with('message', $message);
        } elseif ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : 'Student copied to default store successfully';
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : 'Resell failed.';
        }
        return back()->with('message', $message);
    }
}
