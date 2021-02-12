<?php

class View
{
    public static function partialView($name, $viewBag = array())
    {
        return View::storeRequireIntoText('View/' . $name . '.php', $viewBag);
    }

    public static function storeRequireIntoText($fileToRequire, $inPageData = null)
    {

        if (is_array($inPageData) || is_object($inPageData)) {
            foreach ($inPageData as $key => $data) {
                if($key == "fileToRequire"){
                    continue;
                }
                $$key = $data;
            }
        }

        if (!file_exists($fileToRequire)) {
            $fallback = $_SERVER['DOCUMENT_ROOT'].'/'.$fileToRequire;
            if(!file_exists($fallback)){
                $className = EXCEPTIONCLASS;
                throw new $className("File doesnt exist.", "Could not find - ".$fileToRequire. " or ".$fallback);
            }else{
                $fileToRequire = $fallback;
            }
        };

        ob_start();
        if (!include $fileToRequire) {
            $className = EXCEPTIONCLASS;
            throw new $className("File Produced No Output.", $fileToRequire." was empty....");
        };



        $var = ob_get_contents();
        ob_end_clean();

        if (is_array($inPageData) || is_object($inPageData)) {
            foreach ($inPageData as $key => $data) {
                unset($$key);
            }
        }

        return $var;
    }
}

