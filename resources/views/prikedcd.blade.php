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
            width: 40px; /* Reduced size to 40px */
            height: 40px; /* Reduced size to 40px */
            margin: 20px auto; /* Centering the spinner */
        }
        .lds-spinner div {
            transform-origin: 20px 20px; /* Adjusted for reduced size */
            animation: lds-spinner 1.2s linear infinite;
        }
        .lds-spinner div:after {
            content: " ";
            display: block;
            position: absolute;
            top: 1.6px; /* Adjusted for reduced size */
            left: 18.4px; /* Adjusted for reduced size */
            width: 3.2px; /* Adjusted for reduced size */
            height: 8.8px; /* Adjusted for reduced size */
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
            text-align: left; /* Adjusted for left alignment */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Unused Controller Functions</h1>
        <div class="lds-spinner" id="loader" style="display: none;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
        <div id="loadingText" style="display: none;">Be Patient, Scanning Controllers, It may take few minutes...</div>
        <button class="btn" id="scanButton">Scan Controllers</button>
        <div id="results"></div>
    </div>

    <script>
        document.getElementById('scanButton').addEventListener('click', function() {
            document.getElementById('scanButton').style.display = 'none';
            document.getElementById('loader').style.display = 'block';
            document.getElementById('loadingText').style.display = 'block';

            fetch('/prikedcd_scan')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loader').style.display = 'none';
                    document.getElementById('loadingText').style.display = 'none';
                    document.getElementById('results').innerHTML = '<ul>' + data.unusedFunctions.map(function(fn) {
                        return '<li>' + fn + '</li>';
                    }).join('') + '</ul>';
                });
        });
    </script>
</body>
</html>
