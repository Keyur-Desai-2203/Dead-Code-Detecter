<!DOCTYPE html>
<html>
<head>
    <title>Unused Controller Functions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
            max-height: 80vh; /* Add this line */
            overflow: hidden; /* Add this line */
        }

        #results {
            max-height: 300px; /* Adjust as needed */
            overflow-y: auto; /* Add this line */
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
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background: #eee;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 4px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Unused Controller Functions</h1>
        <div class="lds-spinner" id="loader" style="display: none;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
        <div id="loadingText" style="display: none;">Be Patient, Scanning Controllers, It may take a few minutes...</div>
        <button class="btn" id="scanButton">Scan Controllers</button>
        <div id="progress"></div>
        <div id="results"></div>
    </div>

    <script>
        document.getElementById('scanButton').addEventListener('click', function() {
            document.getElementById('scanButton').style.display = 'none';
            document.getElementById('loader').style.display = 'block';
            document.getElementById('loadingText').style.display = 'block';
            const progressDiv = document.getElementById('progress');
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '';
            let unusedFunctionsCount = 0;

            if (!!window.EventSource) {
                const source = new EventSource("{{ route('scan') }}");

                source.onmessage = function(event) {
                    const data = event.data;

                    // if (data.startsWith("Scanned")) {
                    //     progressDiv.innerHTML = data;
                    // } else if (data.startsWith("Scan complete")) {
                    //     progressDiv.innerHTML = "Scan complete.";
                    //     document.getElementById('loader').style.display = 'none';
                    //     document.getElementById('loadingText').style.display = 'none';
                    // } else {
                    //     const result = JSON.parse(data);
                    //     if (result.unusedFunctions) {
                    //         resultsDiv.innerHTML = '<ul>' + result.unusedFunctions.map(function(fn) {
                    //             return '<li>' + fn + '</li>';
                    //         }).join('') + '</ul>';
                    //     }
                    //     source.close();
                    // }
                    if (data.startsWith("Scanned")) {
                        progressDiv.innerHTML = data;
                    } else {
                        const result = JSON.parse(data);
                        if (result.unusedFunctions) {
                            unusedFunctionsCount = result.unusedFunctions.length;
                            resultsDiv.innerHTML = '<ul>' + result.unusedFunctions.map(function(fn) {
                                return '<li>' + fn + '</li>';
                            }).join('') + '</ul>';
                            progressDiv.innerHTML = `Scan complete. Found ${unusedFunctionsCount} unused functions.`;
                            document.getElementById('loader').style.display = 'none';
                            document.getElementById('loadingText').style.display = 'none';
                        }
                        source.close();
                    }
                };

                source.onerror = function(event) {
                    progressDiv.innerHTML = "An error occurred while scanning.";
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
</body>
</html>
