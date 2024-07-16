@extends('prikedcd::admin.layout.master')

@section('style')
    <style>
        #results {
            max-height: 300px;
            /* Adjust as needed */
            overflow-y: auto;
            /* Add this line */
            /* margin-top: 20px; Add this line */
        }

        .btn {
            background: #3490dc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        .btn:hover {
            background: #2779bd;
        }

        .lds-spinner,
        .lds-spinner div,
        .lds-spinner div:after {
            box-sizing: border-box;
        }

        .lds-spinner {
            color: currentColor;
            display: inline-block;
            position: relative;
            width: 40px;
            height: 40px;
            margin: 20px auto;
        }

        .lds-spinner div {
            transform-origin: 20px 20px;
            animation: lds-spinner 1.2s linear infinite;
        }

        .lds-spinner div:after {
            content: " ";
            display: block;
            position: absolute;
            top: 1.6px;
            left: 18.4px;
            width: 3.2px;
            height: 8.8px;
            border-radius: 20%;
            background: currentColor;
        }

        .lds-spinner div:nth-child(1) {
            transform: rotate(0deg);
            animation-delay: -1.1s;
        }

        .lds-spinner div:nth-child(2) {
            transform: rotate(30deg);
            animation-delay: -1s;
        }

        .lds-spinner div:nth-child(3) {
            transform: rotate(60deg);
            animation-delay: -0.9s;
        }

        .lds-spinner div:nth-child(4) {
            transform: rotate(90deg);
            animation-delay: -0.8s;
        }

        .lds-spinner div:nth-child(5) {
            transform: rotate(120deg);
            animation-delay: -0.7s;
        }

        .lds-spinner div:nth-child(6) {
            transform: rotate(150deg);
            animation-delay: -0.6s;
        }

        .lds-spinner div:nth-child(7) {
            transform: rotate(180deg);
            animation-delay: -0.5s;
        }

        .lds-spinner div:nth-child(8) {
            transform: rotate(210deg);
            animation-delay: -0.4s;
        }

        .lds-spinner div:nth-child(9) {
            transform: rotate(240deg);
            animation-delay: -0.3s;
        }

        .lds-spinner div:nth-child(10) {
            transform: rotate(270deg);
            animation-delay: -0.2s;
        }

        .lds-spinner div:nth-child(11) {
            transform: rotate(300deg);
            animation-delay: -0.1s;
        }

        .lds-spinner div:nth-child(12) {
            transform: rotate(330deg);
            animation-delay: 0s;
        }

        @keyframes lds-spinner {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }
    </style>
@endsection

@section('content')

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="card w-100">
                <div class="card-body">
                    <h5 class="card-title">Basic Instructions</h5>
                    <p class="card-text">
                    <ul>
                        <li>This script will scan your all model functions.</li>
                        <li>After click on scan button please see progess of scanned function out of total buttons.</li>
                        <li><b>Please do not close or not click anywhere before all functions scanned.</b></li>
                        <li>Be Patient untill all functions scanned and then you will get full list of un-used
                            functions.</li>
                    </ul>
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card w-100">
                <div class="card-header">
                    <h5 class="m-0">Scan Models</h5>
                </div>
                <div class="card-body">
                    <p class="card-text" id="start_text">Please click the below button to start....</p>
                    <div class="lds-spinner" id="loader" style="display: none;">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <div id="loadingText" style="display: none;">Be Patient, Scanning, It may take a few minutes...
                    </div>
                    <button class="btn btn-primary" id="scanButton">Scan Models</button>
                    <div id="progress"></div>
                </div>
            </div>
        </div>
        <div class="row" id="unusedFunctionsSection" style="display: none;">
            <div class="card w-100">
                <div class="card-header">
                    <h5 class="m-0">Un-used Model Functions</h5>
                </div>
                <div class="card-body">
                    <div id="results"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('script')
    <script>
        document.getElementById('scanButton').addEventListener('click', function() {
            document.getElementById('scanButton').style.display = 'none';
            document.getElementById('start_text').style.display = 'none';
            document.getElementById('loader').style.display = 'block';
            document.getElementById('loadingText').style.display = 'block';
            const unusedFunctionsSection = document.getElementById('unusedFunctionsSection');
            const progressDiv = document.getElementById('progress');
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '';
            let unusedFunctionsCount = 0;

            if (!!window.EventSource) {
                const source = new EventSource("{{ route('models_scan') }}");
                source.onmessage = function(event) {
                    const data = event.data;
                    if (data.startsWith("Scanned")) {
                        progressDiv.innerHTML = data;
                    } else {
                        const result = JSON.parse(data);
                        if (result.unusedFunctions) {
                            unusedFunctionsCount = result.unusedFunctions.length;
                            resultsDiv.innerHTML = '<ol>' + result.unusedFunctions.map(fn => `<li>${fn}</li>`)
                                .join('') + '</ol>';
                            progressDiv.innerHTML =
                                `Scan complete. Found ${unusedFunctionsCount} unused functions.`;
                            document.getElementById('loader').style.display = 'none';
                            document.getElementById('loadingText').style.display = 'none';
                            unusedFunctionsSection.style.display = 'block';
                        }
                        source.close();
                    }
                };

                source.onerror = function(event) {
                    progressDiv.innerHTML = `Error occurred while scanning`;
                    document.getElementById('loader').style.display = 'none';
                    document.getElementById('loadingText').style.display = 'none';
                    source.close();
                };
            } else {
                progressDiv.innerHTML = "Your browser doesn't support Server-Sent Events.";
                document.getElementById('loader').style.display = 'none';
                document.getElementById('loadingText').style.display = 'none';
            }
        });
    </script>
@endsection