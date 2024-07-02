## Prikedcd Laravel Package

Prikedcd is a Laravel package designed to scan your project for unused functions and display the results in a user-friendly format.

## Features

- Scans Laravel project to identify unused functions.
- Provides a simple interface to trigger the scan via a web route.
- Outputs the list of unused functions with basic styling.

## Installation

You can install the Prikedcd package via Composer. Run the following command in your terminal:

```bash
composer require keyur/prikedcd
```

## Working

After installing package you have to hit route '/prikedcd' and then click on any buttons 'Scan Controllers' or 'Scan Models' button and then wait untill output comes and that process may take time based on project size, so be patient and wait for output.

## Version Information

- Till version 1.0.2 all functionality is applicable for controllers only.

- Now, in version 1.1.0, We have added model scanning also, means now model and controller both can scan individually. 

## Upcoming

- Model and Controller Both can scan together.

- Export output meand fumction names in one file based on date and time of scan and that should be stored and another time updated that file with added new date, time and output. so, user can compare and work accordingly.

- Output will come at run time. Currently output comes after all files scanned. But we are working on output should come at run time. Because if error occured at any point then previously scanned functions output we can see.