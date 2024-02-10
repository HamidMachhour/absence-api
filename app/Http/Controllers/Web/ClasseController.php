<?php

namespace App\Http\Controllers\Web;

use League\Csv\Reader;
use App\Models\Classe;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\ClasseController as V1ClasseController;

class ClasseController extends Controller
{
    // Show all classes
    public function index(Request $request)
    {
        $v1Controller = new V1ClasseController();
        $response=$v1Controller->index($request);
        $params =$this->prepareFilterParams($request,$response);

        return view('classes.index', [
            'classes' => $response,
            'params'=>$params
        ]);
    }

    // Show all authenticated user's classes
    public function manage(Request $request)
    {

        $v1Controller = new V1ClasseController();
        $response=$v1Controller->show($request);
        $params =$this->prepareFilterParams($request,$response);
        return view('classes.manage', [
            'classes' => $response,
            'params' =>$params
        ]);
    }
    public function prepareFilterParams(Request $request, $response)
    {

        $params=[];
        if (!$response || count($response) == 0)
            return $params;

        if ($request->filled('id')) {
            $params['classeId'] = 'القسم:' . $response->first()->name;
        }
        if ($request->filled('userId')) {
            $params['userId'] = 'المدير:' . $response->first()->user->firstName;
        }
        if ($request->filled('tag')) {
            $params['tag'] = 'tag: ' . $request->tag ;
        }
        if ($request->filled('search')) {
            $params['search'] = 'بحث ' . $request->search;
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

    // Store store Data
    public function store(Request $request)
    {
        $controller = new V1ClasseController();
        $response = $controller->store($request);
        // Get the HTTP status code from the response
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array
        if ($status_code === 201) {
            $message = isset($data['message']) ? $data['message'] : 'تمت الاضافة بنجاح';
            return redirect()->route('classes.manage')->with('message', $message);
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم تتم العملية بنجاح، المرجو اعادة المحاولة';
            return back()->withErrors(['message' => $message]);;
        }
    }

    // Show Edit Form
    public function edit(Classe $classe)
    {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        return view('classes.edit', ['classe' => $classe,'addresses'=>Auth::user()->addresses]);
    }

    // Update store Data
    public function update(Request $request, Classe $classe)
    {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('error.route',['message'=>  'غير مسموح']);
        }
        $controller = new V1ClasseController();
        $response = $controller->update($request, $classe);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : 'تمت التعديل بنجاح';
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : 'لم يتم التعديل بنجاح';
        }
        return redirect()->route('classes.edit', ['classe' => $classe])->with('message', $message);
    }

    // Delete store
    public function destroy(Classe $classe)
    {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $controller = new V1ClasseController();
        $response = $controller->destroy($classe);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            $message = isset($data['message']) ? $data['message'] : 'تم الحدف بنجاح';
        } else {
            $message = isset($data['message']) ? $data['message'] : 'الحدف لم تتم بنجاح';
        }
        return redirect()->route('classes.manage')->with('message', $message);
    }

    // set default store
    public function setDefault(Classe $classe)
    {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $controller = new V1ClasseController();
        $response = $controller->setDefault($classe);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
            // update successful
            $message = isset($data['message']) ? $data['message'] : 'Store set as default successfully';
            //get token and save
        } else {
            $message = isset($data['message']) ? $data['message'] : 'Action failed.';
        }
        return redirect()->route('classes.manage')->with('message', $message);
    }

    public function export(Classe $classe)
    {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }
        $controller = new V1ClasseController();
        $response = $controller->export($classe);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array

        if ($status_code === 200) {
            return $response;
        }

        $message = isset($data['message']) ? $data['message'] : 'Action failed.';

        return redirect()->route('classes.manage')->with('message', $message);
    }

    public function importForm(Classe $classe)
    {

        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message', 'Import unauthorized for this store!');
        }
        return view('classes.import', ['classe' => $classe]);

    }

    public function import(Request $request, Classe $classe)
    {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home.route')->with('message',  'غير مسموح');
        }

        $controller = new V1ClasseController();
        $response = $controller->import($request, $classe);
        $status_code = $response->getStatusCode();
        $responseContent = $response->content();
        $data = json_decode($responseContent, true); // true to convert to an associative array


        if ($status_code === 200) {
          //  return $response;
           $message = isset($headers['added']) ? $headers['added']. ' students added successfully!' : 'Import students successully.';
           return redirect()->route('students.manage',['classe'=>$classe->id])->with('message', $message);

        } else{
            $message = isset($data['message']) ? $data['message'] : 'Action failed.';
            return redirect()->route('students.manage',['classe'=>$classe->id])->with('message', $message);
        }
    }
}
