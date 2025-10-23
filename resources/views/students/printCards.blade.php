<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{__('vocab.cards')}} </title>
    <style>
        @page {
            margin: 5mm;
            /* Minimal margin around the A4 page */
        }

        /* Embed a font that supports Arabic characters */
        @font-face {
            font-family: 'amiri';
            /* Ensure the path is correct from the perspective of dompdf */
            src: url('{{ public_path("fonts/Amiri-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'amiri', DejaVu Sans, sans-serif;
            direction: rtl;
            unicode-bidi: bidi-override;
            text-align: right;
            margin: 0;
            padding: 0;
        }

        /* Override direction for non-Arabic content */
        .ltr {
            direction: ltr;
            text-align: left;
        }

        /* Container is the wrapper for the grid */
        .container {
            width: 100%;
            padding: 0px;
        }

        /* The row will act as the grid container for all cards */
        .row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
            margin: 0 0px;
            flex-wrap: wrap;
            page-break-after: auto;
        }

        /*.row {
            display: flex ;
            flex-wrap: wrap;
            margin-left: -10px;
            margin-right: -10px;
            page-break-after: auto;
        }*/

        /* Each card slot is 45% width (two columns) */
        .col-6 {
            flex: 0 0 50%;
            max-width: 45%;
            padding: 5px;
            /* Crucial: keep the card whole, no breaking */
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Card styling */
        .card {
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
            height: 180px;
        }

        .card-body {
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .card-bottom {
            display: flex;
            justify-content: space-between;
            width: 100%;
            font-size: 12px;
            padding-top: 0px;
            margin-top: 0px;
        }

        .card-top {
            display: table;
            width: 100%;
        }

        .card-top-row {
            display: table-row;
        }

        .card-top-cell {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
        }

        /* Utility and specific card styles */
        .info {
            text-align: center;
            font-size: 13px;
            margin: 0px 0;
            padding: 0px;
        }

        .code {
            font-size: 14px;
            font-weight: bold;
        }

        .qr-code {
            width: 100%;
        }

        .student-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Background image for the card - ensure this path and base64 helper works */
        .background-image {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 100px;
            /* The imageToBase64 helper is crucial for remote content */
            background-image: url('{{ imageToBase64(public_path("images/printcard.svg")) }}');
            background-size: cover;
            opacity: 0.15;
            z-index: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            {{-- START OF LOOP --}}
            @foreach ($students as $student)
            @if ($student->user && $student->classe)
            <div class="col-6">
                <div class="card">
                    <div class="background-image"></div>
                    <div class="card-body">
                        <div class="card-top">
                            <div class="card-top-row">
                                {{-- LEFT: QR Code --}}
                                <div class="card-top-cell" style="width: 40%;">
                                    {{-- NOTE: Student object must have been pre-processed to include qrCodeBase64 --}}
                                    <img class="qr-code" src="{{ $student->qrCodeBase64 }}" alt="QRCode" />
                                </div>

                                {{-- CENTER: Code, Section, Number --}}
                                <div class="card-top-cell" style="width: 30%;">
                                    <p class="info code">{{ $student->user->code }}</p>
                                    <p class="info"><br>{{ $student->al9ism}}</p>
                                    <p class="info">{{ $student->ara9m}}</p>
                                </div>

                                {{-- RIGHT: Student Image and Name --}}
                                <div class="card-top-cell" style="width: 30%;">
                                    {{-- NOTE: Student object must have been pre-processed to include studentImageBase64 --}}
                                    <img class="student-img" src="{{ $student->studentImageBase64 }}" alt="Student Image">
                                    <p class="info">
                                        {{ $student->fullName }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- BOTTOM: Email and School Year --}}
                        <div class="card-bottom">
                            <p class="info m-0 ltr ">{{ $student->user->email .'    '.$student->schoolYear() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            {{-- END OF LOOP --}}
        </div>
    </div>
</body>

</html>