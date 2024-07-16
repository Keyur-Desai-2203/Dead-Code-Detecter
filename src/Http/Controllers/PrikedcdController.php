<?php

// namespace Keyur\Prikedcd\Http\Controllers;

// use App\Http\Controllers\Controller;
// use RecursiveIteratorIterator;
// use RecursiveDirectoryIterator;
// use ReflectionClass;
// use Illuminate\Support\Facades\File;

// class PrikedcdController extends Controller
// {
//     public function index()
//     {
//         $baseNamespace = 'App\\Http\\Controllers';

//         $controllersPath = base_path('app/Http/Controllers');
//         $projectPath = base_path();

//         function getPhpFiles($dir)
//         {
//             $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
//             $files = array();

//             foreach ($rii as $file) {
//                 if ($file->isDir()) {
//                     continue;
//                 }
//                 if ($file->getExtension() == 'php') {
//                     $files[] = $file->getPathname();
//                 }
//             }

//             return $files;
//         }

//         function getClassName2($filePath, $baseNamespace, $controllersPath)
//         {
//             $relativePath = str_replace($controllersPath, '', $filePath);
//             $relativePath = trim($relativePath, DIRECTORY_SEPARATOR);
//             $relativePath = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
//             $relativePath = str_replace('.php', '', $relativePath);
//             return $baseNamespace . '\\' . $relativePath;
//         }

//         function getClassName($filePath, $baseNamespace)
//         {
//             $contents = file_get_contents($filePath);

//             if (preg_match('/class\s+([a-zA-Z0-9_]+)/', $contents, $matches)) {
//                 $className = $matches[1];

//                 return $baseNamespace . '\\' . $className;
//             }

//             return null;
//         }

//         function isFunctionCalled($functionName, $projectPath)
//         {
//             $files = getPhpFiles($projectPath);
//             $pattern = '/(?:->\s*' . preg_quote($functionName, '/') . '\s*\(|\'\s*' . preg_quote($functionName, '/') . '\'\s*|\:\:\s*' . preg_quote($functionName, '/') . '\s*\()/';
//             foreach ($files as $file) {
//                 $contents = file_get_contents($file);
//                 if (preg_match($pattern, $contents)) {
//                     return true;
//                 }
//             }
//             return false;
//         }

//         $files = getPhpFiles($controllersPath);
//         $unusedFunctions = [];
//         $validFiles = [];
//         foreach ($files as $file) {
//             $fullClassName = getClassName($file, $baseNamespace, $controllersPath);
//             $className = basename($file, '.php');
//             $fullClassNameParts = explode('\\', $fullClassName);
//             $extractedClassName = end($fullClassNameParts);

//             if ($className === $extractedClassName) {
//                 $validFiles[] = $file;
//             }
//         }
//         foreach ($validFiles as $file) {
//             $fullClassName = getClassName2($file, $baseNamespace, $controllersPath);

//             if (class_exists($fullClassName)) {
//                 $class = new ReflectionClass($fullClassName);

//                 $methods = $class->getMethods();

//                 foreach ($methods as $method) {
//                     $methodName = $method->name;

//                     if ($method->class !== $fullClassName) {
//                         continue;
//                     }

//                     if ($methodName === '__construct' || strpos($methodName, '__') === 0) {
//                         continue;
//                     }

                    
//                     if (!isFunctionCalled($methodName, $projectPath)) {
//                         $unusedFunctions[] = "{$fullClassName}::{$methodName}";
//                     }
//                 }
//             }
//         }

//         return view('prikedcd::prikedcd', compact('unusedFunctions'));
//     }
// }

namespace Keyur\Prikedcd\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use ReflectionClass;

class PrikedcdController extends Controller
{
    public function index()
    {
        return view('prikedcd::prikedcd');
    }

    public function model_scan() 
    {
        $baseNamespace = 'App\\Models';
        $controllersPath = base_path('app/Models');
        $projectPath = base_path();

        $files = $this->getPhpFiles($controllersPath);
        $unusedFunctions = [];
        $validFiles = [];

        foreach ($files as $file) {
            $fullClassName = $this->getClassName($file, $baseNamespace, $controllersPath);
            $className = basename($file, '.php');
            $fullClassNameParts = explode('\\', $fullClassName);
            $extractedClassName = end($fullClassNameParts);

            if ($className === $extractedClassName) {
                $validFiles[] = $file;
            }
        }

        $totalFunctions = 0;
        $scannedFunctions = 0;

        // Calculate total functions
        foreach ($validFiles as $file) {
            $fullClassName = $this->getClassName2($file, $baseNamespace, $controllersPath);

            if (class_exists($fullClassName)) {
                $class = new ReflectionClass($fullClassName);
                $methods = $class->getMethods();

                foreach ($methods as $method) {
                    $methodName = $method->name;
                    $methodFile = $method->getFileName();
                    
                    if ($method->class !== $fullClassName || $methodName === '__construct' || strpos($methodName, '__') === 0 || strpos($methodFile, 'vendor') !== false) {
                        continue;
                    }

                    $totalFunctions++;
                }
            }
        }

        // Create a StreamedResponse to send data as a stream
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($validFiles, $baseNamespace, $controllersPath, $projectPath, &$scannedFunctions, &$unusedFunctions, $totalFunctions) {
            foreach ($validFiles as $file) {
                $fullClassName = $this->getClassName2($file, $baseNamespace, $controllersPath);

                if (class_exists($fullClassName)) {
                    $class = new ReflectionClass($fullClassName);
                    $methods = $class->getMethods();

                    foreach ($methods as $method) {
                        $methodName = $method->name;
                        $methodFile = $method->getFileName();
                        if ($method->class !== $fullClassName || $methodName === '__construct' || strpos($methodName, '__') === 0 || strpos($methodFile, 'vendor') !== false) {
                            continue;
                        }

                        $scannedFunctions++;

                        if (!$this->isFunctionCalled($methodName, $projectPath)) {
                            $unusedFunctions[] = "{$fullClassName}::{$methodName}";
                        }

                        // Send progress to the front end
                        echo "data: Scanned $scannedFunctions of $totalFunctions functions.\n\n";
                        ob_flush();
                        flush();
                    }
                }
            }

            echo "data: Scan complete.\n\n";
            echo "data: " . json_encode(['unusedFunctions' => $unusedFunctions]) . "\n\n";
            ob_flush();
            flush();
        });

        // Set the headers for the StreamedResponse
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    public function scan()
{
    $baseNamespace = 'App\\Http\\Controllers';
    $controllersPath = base_path('app/Http/Controllers');
    $projectPath = base_path();

    $files = $this->getPhpFiles($controllersPath);
    $unusedFunctions = [];
    $validFiles = [];

    foreach ($files as $file) {
        $fullClassName = $this->getClassName($file, $baseNamespace, $controllersPath);
        $className = basename($file, '.php');
        $fullClassNameParts = explode('\\', $fullClassName);
        $extractedClassName = end($fullClassNameParts);

        if ($className === $extractedClassName) {
            $validFiles[] = $file;
        }
    }

    $totalFunctions = 0;
    $scannedFunctions = 0;

    // Calculate total functions
    foreach ($validFiles as $file) {
        $fullClassName = $this->getClassName2($file, $baseNamespace, $controllersPath);

        if (class_exists($fullClassName)) {
            $class = new ReflectionClass($fullClassName);
            $methods = $class->getMethods();

            foreach ($methods as $method) {
                $methodName = $method->name;
                $methodFile = $method->getFileName();

                if ($method->class !== $fullClassName || $methodName === '__construct' || strpos($methodName, '__') === 0 || strpos($methodFile, 'vendor') !== false) {
                    continue;
                }

                $totalFunctions++;
            }
        }
    }

    // Create a StreamedResponse to send data as a stream
    $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($validFiles, $baseNamespace, $controllersPath, $projectPath, &$scannedFunctions, &$unusedFunctions, $totalFunctions) {
        foreach ($validFiles as $file) {
            $fullClassName = $this->getClassName2($file, $baseNamespace, $controllersPath);

            if (class_exists($fullClassName)) {
                $class = new ReflectionClass($fullClassName);
                $methods = $class->getMethods();

                foreach ($methods as $method) {
                    $methodName = $method->name;
                    $methodFile = $method->getFileName();
                    if ($method->class !== $fullClassName || $methodName === '__construct' || strpos($methodName, '__') === 0 || strpos($methodFile, 'vendor') !== false) {
                        continue;
                    }

                    $scannedFunctions++;

                    if (!$this->isFunctionCalled($methodName, $projectPath)) {
                        $unusedFunctions[] = "{$fullClassName}::{$methodName}";
                    }

                    // Send progress to the front end
                    echo "data: Scanned $scannedFunctions of $totalFunctions functions.\n\n";
                    ob_flush();
                    flush();
                }
            }
        }

        echo "data: Scan complete.\n\n";
        echo "data: " . json_encode(['unusedFunctions' => $unusedFunctions]) . "\n\n";
        ob_flush();
        flush();
    });

    // Set the headers for the StreamedResponse
    $response->headers->set('Content-Type', 'text/event-stream');
    $response->headers->set('Cache-Control', 'no-cache');
    $response->headers->set('Connection', 'keep-alive');

    return $response;
}


    private function getPhpFiles($dir)
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $files = [];

        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }

             // Skip the vendor folder
        if (strpos($file->getPathname(), DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false) {
            continue;
        }
            if ($file->getExtension() == 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function getClassName2($filePath, $baseNamespace, $controllersPath)
    {
        $relativePath = str_replace($controllersPath, '', $filePath);
        $relativePath = trim($relativePath, DIRECTORY_SEPARATOR);
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
        $relativePath = str_replace('.php', '', $relativePath);
        return $baseNamespace . '\\' . $relativePath;
    }

    private function getClassName($filePath, $baseNamespace)
    {
        $contents = file_get_contents($filePath);

        if (preg_match('/class\s+([a-zA-Z0-9_]+)/', $contents, $matches)) {
            $className = $matches[1];
            return $baseNamespace . '\\' . $className;
        }

        return null;
    }

    private function isFunctionCalled($functionName, $projectPath)
    {
        $files = $this->getPhpFiles($projectPath);
        // $pattern = '/(?:->\s*' . preg_quote($functionName, '/') . '\s*\(|\'\s*' . preg_quote($functionName, '/') . '\'\s*|\:\:\s*' . preg_quote($functionName, '/') . '\s*\()/';
        $pattern = '/(?:->\s*' . preg_quote($functionName, '/') . '\s*\(|\'\s*' . preg_quote($functionName, '/') . '\'\s*|\:\:\s*' . preg_quote($functionName, '/') . '\s*\(|@\s*' . preg_quote($functionName, '/') . '\s*\()/';

        foreach ($files as $file) {
            $contents = file_get_contents($file);
            if (preg_match($pattern, $contents)) {
                return true;
            }
        }
        return false;
    }
}


