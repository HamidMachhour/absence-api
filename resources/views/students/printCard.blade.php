<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{__('vocab.cards')}} </title>
    <style>
        /* Embed a font that supports Arabic characters */
        @font-face {
            font-family: 'amiri';
            src: url('{{ public_path("fonts/Amiri-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }


        body {
            font-family: 'amiri', DejaVu Sans, sans-serif;
            direction: rtl;
            unicode-bidi: bidi-override;
            text-align: right;
        }

        /* Override direction for non-Arabic content */
        .ltr {
            direction: ltr;
            text-align: left;
        }

        /* A self-contained Bootstrap-like CSS grid for two columns */
        .container {
            width: 100%;
            padding: 10px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-left: -10px;
            margin-right: -10px;
        }

        .col-6 {
            flex: 0 0 48%;
            max-width: 48%;
            padding: 5px;
            box-sizing: border-box;
        }

        /* Card styling to match the image */
        .card {
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            page-break-inside: avoid;
            position: relative;
            height: 180px;
            /* Set a fixed height to maintain aspect ratio */
        }

        .card-body {
            padding: 10px;
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
            padding-top: 5px;
            margin-top: auto;
        }


        /* Top section with QR, info, and image */
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
            vertical-align: top;
        }



        /* Inner grid for card content */
        .col-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 0;
            text-align: center;
        }

        /* Utility classes */
        .d-flex {
            display: flex;
        }

        .flex-column {
            flex-direction: column;
        }

        .justify-content-start {
            justify-content: flex-start;
        }

        .align-items-center {
            align-items: center;
        }

        .p-0 {
            padding: 0 !important;
        }

        .m-0 {
            margin: 0 !important;
        }

        /* Card-specific styling */
        .info {
            text-align: center;
            font-size: 12px;
            margin: 5px 0;
        }

        .code {
            font-size: 14px;
            font-weight: bold;
        }

        .qr-code {
            width: 120px;
            height: 120px;
        }

        .student-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Background image for the card */
        .background-image {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 100px;
            background-image: url('{{ imageToBase64(public_path("images/printcard.svg")) }}');
            background-size: cover;
            opacity: 0.15;
            z-index: 0;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .card {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <!-- Each card is a column in the row -->
            <div class="col-6">
                <div class="card">
                    <div class="background-image"></div>
                    <div class="card-body">
                        <div class="card-top">
                            <div class="card-top-row">
                                <!-- QR Code -->
                                <div class="card-top-cell" style="width: 33.33%;">
                                    <img class="qr-code" src="{{ $student->qrCodeBase64 }}" alt="QRCode" />
                                </div>
                                <!-- Code, Section, Number -->
                                <div class="card-top-cell" style="width: 33.33%;">
                                    <p class="info code">{{ $student->user->code }}</p>
                                    <p class="info"><br>{{ $student->al9ism}}</p>
                                    <p class="info">{{ $student->ara9m}}</p>
                                </div>
                                <!-- Student Image and Name -->
                                <div class="card-top-cell" style="width: 33.33%;">
                                    <img class="student-img" src="{{ $student->studentImageBase64 }}" alt="Student Image">
                                    <p class="info">
                                        {{ $student->fullName }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Bottom Info -->
                        <div class="card-bottom">
                            <p class="info m-0 ltr ">{{ $student->user->email .'    '.$student->schoolYear() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>