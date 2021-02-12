<?php

class ErrorHandler
{

    public static function HandlePHPError($errno, $errstr, $errfile, $errline)
    {
        if ($errno == 0 || $errno == 2 || $errno == 8) {
            return;
        }

        if (!StringFunctions::endsWith($errfile, "Core\requires.php")) {
            //DOESNT NEED TO BE IN TRY CATCH AS IT REMAINS IN THE CODE CONTEXT
            $className = EXCEPTIONCLASS;
            throw new $className(
                "Error - " . $errno,
                "<strong>File:</strong> " . $errfile . " <strong>Line: </strong>" . $errline . " - " . $errstr,
                "Core Error",
                debug_backtrace()
            );
        }
    }

    public static function HandlePHPFatal()
    {
        $error = error_get_last();
        $className = EXCEPTIONCLASS;

        if ($error['type'] != 1 && $error['type'] != 64) {
            return;
        }

        if (!CRUD::$connection) {
            if (DEV) {
                die("A database connection error occured - In Addition to <br/>Fatal Error - 1 <strong>File:</strong> $errfile <strong>Line: </strong>$errline $errstr");
            }
            die('Error - 60492');
        } else {
            CRUD::cleanup();
        }



        $className = EXCEPTIONCLASS;
        $file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $error['file']);


        $mainError =  '<h4>File:</h4> ' . $file . '<br/>';
        $mainError .= '<h4>Line:</h4> ' . $error['line'] . '<br/>';
        $mainError .= '<h4>Error: </h4>' . $error['message'] . '<br/>';

        if(stristr($error['message'], 'Allowed memory size')) {
            if (DEV) {
                echo $mainError;
            }

            die('Error - 69781');
        }

        $e = new $className(
            "Fatal Error (1)",
            $mainError,
            $file
        );
        echo $e->getExceptionMessage();

    }
}


