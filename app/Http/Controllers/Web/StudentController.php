<?php

namespace App\Http\Controllers\Web;

use Exception;
use App\Models\Classe;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\StudentController as V1StudentController;
use GuzzleHttp\Psr7\Response;

class StudentController extends Controller
{
    // Show all students
    public function index(Request $request)
    {
        try {

            $v1Controller = new V1StudentController();
            $response = $v1Controller->index($request);

            if (method_exists($response, 'status') && $response->status() == 404){
                return view('error', ["message" => ' غير متوفر']);
            }

            $params = $this->prepareFilterParams($request, $response);

            //dd($response);

            return view('students.index', [
                'students' => $response,
                'params' => $params
            ]);
        } catch (Exception $e) {
            return view('error', ["message" => 'غير متوفر' . $e->getMessage()]);
        }
    }

    // Show all authenticated user's students
    public function manage(Request $request, Classe $classe)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $classe->userId) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $v1Controller = new V1StudentController();

        $response = $v1Controller->show($request, $classe);
        // Get the HTTP status code from the response
        if (method_exists($response, 'status') && $response->status() == 404)
            return view('error', ["message" => ' غير متوفر']);
        $params = $this->prepareFilterParams($request, $response);
        $params['classeId'] = 'القسم ' .$classe->name;
        return view('students.manage', [
            'classe' => $classe,
            'students' => $response,
            'params' => $params
        ]);
    }
    public function prepareFilterParams(Request $request, $response)
    {

        $params=[];
        if (!$response || count($response) == 0)
            return $params;

        if ($request->filled('orderBy')) {
            $params['orderBy'] = 'مع الترتيب';
        }
        if ($request->filled('classeId')) {
            $params['classeId'] = 'القسم ' . $response->first()->classe->name;
        }
        if ($request->filled('userId')) {
            $params['userId'] = 'التلميد ' . $response->first()->user->firstName;
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
            $params['search'] = 'بحث ' . $request->search;
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
    public function store(Request $request)
    {


        $controller = new V1StudentController();
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : 'Student created!';
            return redirect()->route('students.manage', ['classe' => $request->get('classeId')])->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم تتم العملية بنجاح، المرجو اعادة المحاولة';
            return back()->withErrors(['message' => $message]);;
        }
    }

    // Show Edit Form
    public function edit(Student $student)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $student->classe->userId) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $classes = Classe::where('userId', $student->classe->userId)->get();
        return view('students.edit', ['student' => $student, 'classes' => $classes]);
    }

    // Update student Data
    public function update(Request $request, Student $student)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->id !== $student->classe->userId) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
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
            $message = isset($data['message']) ? $data['message'] : 'لم يتم التعديل بنجاح';
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
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }

        $controller = new V1StudentController();
        $response = $controller->destroy($student);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code == 200) {
            $message = isset($data['message']) ? $data['message'] : 'تم الحدف بنجاح';
        } else {
            $message = isset($data['message']) ? $data['message'] : 'الحدف لم يتم بنجاح';
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
