<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use BaconQrCode\Writer;
use Milon\Barcode\DNS1D;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\URL;
use BaconQrCode\Renderer\ImageRenderer;
use Illuminate\Database\Eloquent\SoftDeletes;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

/**
 * Class Student
 *
 * @property string|null $id
 * @property int $num
 * @property string $deleted_at
 * @property string $userId
 * @property string|null $classeId
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class Student extends AbsenceModel
{


    protected $table = 'Student';

    protected $casts = [
        'num' => 'int',
        'absences_sum_heure' => 'float'
    ];

    protected $fillable = [
        'num',
        'userId',
        'classeId',
    ];

    protected static function booted()
    {
        parent::boot();

        static::deleting(function ($student) {
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($student) {
                $student->misconductReports()->delete();
                $student->absences()->delete();
                $student->attendances()->delete();
            });
        });
    }

    public function scopeFilter($query, Request $request)
    {

        $query->when($request->filled('id'), function ($q) use ($request) {
            $q->where('id', $request->id);
        });

        $query->when($request->filled('classeId'), function ($q) use ($request) {
            $q->where('classeId', $request->classeId);
        });
        $query->when($request->filled('userId'), function ($q) use ($request) {
            $q->where('userId', $request->userId);
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->whereHas('user', function ($user) use ($request) {
                $user->where('firstName', 'like', '%' . $request->search . '%')
                    ->orWhere('lastName', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
            $q->orWhere('num', $request->search);
        });

        if ($request->filled('minPlage') || $request->filled('maxPlage')) {
            $maxPlage = floatval($request->maxPlage);
            $minPlage = floatval($request->minPlage);
            if ($request->filled('minPlage') && $request->filled('maxPlage') && $minPlage > $maxPlage) {
                $t = $minPlage;
                $minPlage = $maxPlage;
                $maxPlage = $t;
            }
            $query->when($request->filled('minPlage'), fn($q) => $q->having('absences_sum_plage', '>=', $minPlage));
            $query->when($request->filled('maxPlage'), fn($q) => $q->having('absences_sum_plage', '<=', $maxPlage));
        }


        if ($request->filled('minAbsences') || $request->filled('maxAbsences')) {
            $maxAbsences = floatval($request->maxAbsences);
            $minAbsences = floatval($request->minAbsences);
            if ($request->filled('minAbsences') && $request->filled('maxAbsences') && $minAbsences > $maxAbsences) {
                $t = $minAbsences;
                $minAbsences = $maxAbsences;
                $maxAbsences = $t;
            }
            $query->when($request->filled('minAbsences'), fn($q) => $q->having('absences_count', '>=', $minAbsences));
            $query->when($request->filled('maxAbsences'), fn($q) => $q->having('absences_count', '<=', $maxAbsences));
        }

        if ($request->filled('orderBy')) {
            $query->orderByRaw($request->orderBy);
        } else {
            $query->orderBy('num');
        }
        return $query;
    }



    public function misconductReports()
    {
        return $this->hasMany(MisconductReport::class, 'studentId');
    }

    public function setAbsencesSumPlageAttribute($value)
    {
        $this->attributes['absences_sum_plage'] = $value ?? 0.0;
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classeId');
    }

    public function absences()
    {
        return $this->hasMany(AbsenceStudent::class, 'studentId');
    }

    public function attendances()
    {
        return $this->hasMany(AttendanceStudent::class, 'studentId');
    }

    public function studentImage()
    {
        return $this->user->userImage();
    }

    public function hasCut()
    {
        return $this->absences()->whereNull('justification')->exists();
    }

    public function barcodeImagePath()
    {
        // The data to be encoded.
        // JSON format is used to handle multiple pieces of information.
        $dataToEncode = json_encode([
            'student_id' => $this->id,
            'tag_id' => $this->user->tag_id,
        ]);

        // Directory where barcode images are stored
        $barcodeDirectory = public_path('assets/images/barcodes/');

        // Use a hash of the data to create a unique filename.
        // This is better than just the ID if the data can change.
        $filename = md5($dataToEncode) . '.png';
        $barcodeImagePath = $barcodeDirectory . $filename;

        // Check if the image already exists
        if (!file_exists($barcodeImagePath)) {
            // Ensure the directory exists
            if (!file_exists($barcodeDirectory)) {
                mkdir($barcodeDirectory, 0755, true);
            }

            // Generate barcode image if it doesn't exist
            $barcode = new DNS1D();
            $barcode->setStorPath($barcodeDirectory);

            // Generate the barcode PNG with the serialized data
            $barcodeImage = $barcode->getBarcodePNG($dataToEncode, "C128");

            // Decode and save the barcode image
            $decodedBarcodeImage = base64_decode($barcodeImage);
            file_put_contents($barcodeImagePath, $decodedBarcodeImage);
        }

        // Return the URL to the barcode image
        return URL::asset('assets/images/barcodes/' . $filename);
    }
    // In your Student model

    // This method ensures the file exists and returns its local path.
    // It is the single source of truth for file creation.
    // In your Student model

    // This method ensures the file exists and returns its local path.
    // It is the single source of truth for file creation.
    public function getOrCreateQrCodePath(): string
    {
        // The data to be encoded
        $dataToEncode = json_encode([
            'student_id' => $this->id,
            'tag_id' => $this->user->tag_id,
        ]);

        $qrCodeDirectory = public_path('assets/images/qrcodes/');

        // Use a hash of the data to create a unique filename.
        $filename = md5($dataToEncode) . '-m1.svg';
        $qrCodeImagePath = $qrCodeDirectory . $filename;

        // Ensure the directory exists
        if (!file_exists($qrCodeDirectory)) {
            mkdir($qrCodeDirectory, 0755, true);
        }

        // Generate the QR code only if the file doesn't exist
        if (!file_exists($qrCodeImagePath)) {
            $renderer = new ImageRenderer(
                new RendererStyle(400, 1),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);

            // Write the serialized data to the QR code
            $writer->writeFile($dataToEncode, $qrCodeImagePath);
        }

        return $qrCodeImagePath;
    }

    // This method returns the local path for PDF generation.
    public function qrCodeImagePath(): string
    {
        return $this->getOrCreateQrCodePath();
    }

    // This method returns the web URL for browser view.
    public function qrCodeImageUrl(): string
    {
        // The data to be encoded
        $dataToEncode = json_encode([
            'student_id' => $this->id,
            'tag_id' => $this->user->tag_id,
        ]);

        // Use a hash of the data to create a unique filename.
        $filename = md5($dataToEncode) . '-m1.svg';
        $relativePath = 'assets/images/qrcodes/' . $filename;

        // Ensure the file exists by calling the shared creation method
        $this->getOrCreateQrCodePath();

        // Return the web-accessible URL
        return asset($relativePath);
    }
    public static function nbAbsences($userId)
    {
        $student = Student::where('userId', $userId)->latest()->first();

        if ($student)
            return count($student->absences()->get());

        return 0;
    }

    public function schoolYear()
    {
        // Get the year from the created_at date
        $year = date('Y', strtotime($this->created_at));

        // Determine the school year based on the month
        $startYear = $this->created_at->format('n') >= 9 ? $year : $year - 1;
        $endYear = $startYear + 1;

        // Format the school year
        return $startYear . '/' . $endYear;
    }
}
