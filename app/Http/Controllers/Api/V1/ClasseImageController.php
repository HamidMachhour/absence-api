<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;
use App\Models\ClasseImage;
use App\Models\Classe;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class ClasseImageController extends Controller
{

    public function show(Request $request, $page_size = 6)
    {
        try {
            if ($request->filled('id')) { // image with id
                $id = $request->input('id');
                return ClasseImage::whereHas(
                    'classe',
                    function ($classe) {
                        $classe->where('userId', auth()->user()->id);
                    }
                )
                    //-> with('classe')
                    ->findOrFail($id);
            } elseif ($request->filled('classeId')) { //images of classeId
                $classeId = $request->input('classeId');
                return ClasseImage::where('classeId', $classeId)
                    ->whereHas(
                        'classe',
                        function ($classe) {
                            $classe->where('userId', auth()->user()->id);
                        }
                    )
                    //-> with('classe')
                    ->paginate($page_size);
            } else { // all user classes images
                return ClasseImage::whereHas('classe', function ($classe) {
                    $classe->where('userId', auth()->user()->id);
                })
                    //->with('classe')
                    ->paginate($page_size);
            }
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
        }
    }

    public function setDefault(Classe $classe, ClasseImage $image)
    {
        if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => __('vocab.not_authorized')], 403);
        }
        try {
            $classe->update(['imageId' => $image->id]);
            return response()->json(['message' => 'Update successfully', 'classe' => $classe], 200);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.update_failure').' '.__('vocab.try_again') . $e->getMessage()], 404);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'uri' => 'required|string',
            'classeId' => 'nullable|string',
            'id' => 'nullable|string',
        ]);

        try {
            if ($request->filled('classeId')) {
                $classeId = $validatedData['classeId'];
                $classe = Classe::findOrFail($classeId);

                if (Auth::user()->id !== $classe->userId && !Auth::user()->hasRole('admin')) {
                    return response()->json(['message' => __('vocab.not_authorized')], 403);
                }
                // Create a new ClasseImage object and assign the validated data
                $validatedData['classeId'] = $classe->id;
                $classeImage = ClasseImage::create($validatedData);
                if (!$classe->imageId)
                    $classe->update(['imageId' => $classeImage->id]);
                return response()->json(['message' => __('vocab.action_success'), 'classeImage' => $classeImage], 201);
            }

            return response()->json(['message' => __('vocab.task_failed').' '.__('vocab.try_again') . $exception->getMessage()], 404);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.task_failed').' '.__('vocab.try_again') . $exception->getMessage()], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'uri' => 'required|string',
            'id' => 'required|string',
        ]);
        try {
            $id = $validatedData['id'];
            unset($validatedData['id']);
            $classeImage = ClasseImage::findOrFail($id);
            if (Auth::user()->id !== $classeImage->classe->userId && !Auth::user()->hasRole('admin')) {
                return response()->json(['message' => __('vocab.not_authorized')], 403);
            }
            // Save the classeImage
            $classeImage->update($validatedData);
            // Retrieve the updated classeImage from the database
            $updatedClasseImage = ClasseImage::with('classe')->findOrFail($id);
            return response()->json(['message' => __('vocab.update_success'), 'classeImage' => $updatedClasseImage], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => __('vocab.update_failure').' '.__('vocab.try_again') . $exception->getMessage()], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(ClasseImage $image)
    {
        try {
            if (Auth::user()->id !== $image->classe->userId && !Auth::user()->hasRole('admin')) {
                return response()->json(['message' => __('vocab.not_authorized')], 403);
            }
            // Soft delete the classeImage
            $image->delete();
            // Return a response
            return response()->json(['message' => 'Deleted successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => __('vocab.no_data') . $e->getMessage()], 404);
        }
    }


    public function upload(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:csv|max:10240',
            'classeId' => 'nullable|string',
        ]);
        $not_imported = [];
        $updated = [];
        $added = [];

        try {
            // Store the file locally
            $file = $request->file('file');
            $fileName = 'store-images-' . Str::uuid() . '.csv';
            $filePath = $file->storeAs('imports', $fileName, 'public');
            //default store
            if ($request->filled('classeId')) {
                $classeId = $request->input('classeId');
                $classe = Classe::where('userId', auth()->user()->id)
                    ->find($classeId);
                if (!$classe)
                    unset($classeId);
            }

            // Read the CSV file
            $csv = Reader::createFromPath(storage_path('app/public/' . $filePath), 'r');

            $csv->setHeaderOffset(0);

            // Import the classeImages from the CSV
            foreach ($csv->getRecords() as $record) {
                $imageData = [
                    'id' => $record['id'],
                    'uri' => $record['uri'],
                    'classeId' => $record['classeId'],
                ];

                if (isset($classeId) && $classeId) {
                    $imageData['classeId'] = $classeId;
                } else {
                    $classe = Classe::where('userId', auth()->user()->id)
                        ->find($imageData['classeId']);
                    if (!$classe) {
                        $not_imported[] = $imageData['id'];
                        continue;
                    }
                }

                // Create/update ClasseImage model and save it to the database
                try {
                    $image = ClasseImage::where('classeId', $imageData['classeId'])
                        ->find($imageData['id']);
                    if ($image) {
                        $image->update($imageData);
                        $updated[] = $imageData['id'];
                    } else {
                        ClasseImage::create($imageData);
                        $added[] = $imageData['id'];
                    }
                } catch (Exception $e) {
                    $not_imported[] = $imageData['id'];
                }
            }
            // Delete the temporary CSV file
            File::delete(storage_path('app/public/' . $filePath));
            // Return a response
            return response()->json([
                'message' => 'Import summary:' .
                    count($added) . ' added, ' .
                    count($updated) . ' updated, ' .
                    count($not_imported) . ' not imported.',
                'not_imported' => $not_imported,
                'updated' => $updated,
                'added' => $added,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Import failed.' . $e->getMessage()
            ], 404);
        }
    }



    public function download(Request $request)
    {
        try {
            $images = [];
            if ($request->filled('classeId')) {
                $classeId = $request->input('classeId');
                $classe = Classe::where('userId', auth()->user()->id)
                    ->find($classeId);
                if ($classe)
                    $images = $classe->classeImages()->get();
            } else {
                $images = ClasseImage::whereHas('classe', function ($classe) {
                    $classe->where('userId', auth()->user()->id);
                })->get();
            }

            // Generate the filename for the exported file
            $fileName = 'store-images-' . time() . '.csv';


            $csv = Writer::createFromFileObject(new SplTempFileObject());

            // Add headers to the CSV
            $headers = ['id', 'uri', 'classeId'];
            $csv->insertOne($headers);

            // Add images to the CSV
            foreach ($images as $image) {
                $imageData = [
                    $image->id,
                    $image->uri,
                    $image->classeId,
                ];
                $csv->insertOne($imageData);
            }
            // Set the response headers
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName,
            ];

            // Return the CSV file as a downloadable response
            return Response::make($csv->toString(), 200, $headers);
        } catch (Exception $exception) {
            return response()->json(['message' => 'Downloading Failed. ' . $exception->getMessage()], 404);
        }
    }
}
