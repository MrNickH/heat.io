<?php
/**
 * Description of Login
 *
 * @author Nick
 */
class CORE_Navigation
{
    private $allowedNavs;

    private $controller;
    private $parentControllers = [];
    private $function;
    private $parameters;
    private $viewfile;
    private $baseLog;

    public function __construct() {
        $this->allowedNavs = ALLOWEDNAVS;
    }

    public function get_output()
    {

        if ($_GET['P_one'] == "index.php" || $_GET['P_one'] == "") {
            $_GET['P_one'] = 'home';
        }

        $this->determineControllerAndFunction();

        $controllerName = $this->controller;
        $controllerFunction = $this->function;
        $controllerObject = new $controllerName();

        //Check Allowed Functions
        if (($allowed = $this->checkAllowed($controllerObject)) !== true) {
            if (is_numeric($allowed)) {
                $this->dieToError($allowed);
            }

            if (is_array($allowed)) {
                return $this->runView($allowed);
            }

            $this->dieToError(403);

        }

        //Call Main Functions
        $controllerData = $controllerObject->$controllerFunction(...$this->parameters) ?? [];

        //Call Alls
        $controllerData = $this->viewAlls($controllerObject, $controllerData);


        return $this->runView($controllerData);
    }

    private function checkAllowed($controllerObject)
    {
        if (method_exists($controllerObject, "controller_allowed")) {
            $allowed = $controllerObject->controller_allowed();

            if($allowed !== true){
                return $allowed;
            }
        }

        foreach ($this->parentControllers as $parentController) {
            if($this->checkController($parentController)) {
                $parentControllerName = self::getNameSpacedController($parentController);
                $parentController = new $parentControllerName();
                if (method_exists($parentController, "controller_allowed")) {
                    $allowed = $parentController->controller_allowed();

                    if($allowed !== true){
                        return $allowed;
                    }
                }
            }
        }

        return true;
    }

    private function viewAlls($controllerObject, $controllerData)
    {
        if (method_exists($controllerObject, "view_all")) {
            $controllerData = $controllerObject->view_all($controllerData);
        }

        foreach ($this->parentControllers as $parentController) {
            if($this->checkController($parentController)) {
                $parentControllerName = self::getNameSpacedController($parentController);
                $parentController = new $parentControllerName();
                if (method_exists($parentController, "view_all")) {
                    $controllerData = $parentController->view_all($controllerData);
                }
            }
        }

        if (method_exists('\Controller\home', "view_all")) {
            $controllerData = \Controller\home::view_all($controllerData);
        }

        return $controllerData;
    }

    private function determineControllerAndFunction()
    {
        $arguments = &$_GET;
        $parameters = [];
        $fullClassToTry = "";
        $classesToTry = [];
        $fullClassesToTry = [];

        foreach($_GET as $key => &$parameter){
            if(StringFunctions::startsWith($key, "P_")){
                $parameter = $this->dealWithParameter($parameter);
                $parameters[] = $parameter;


                $classToTry = $parameter;
                $fullClassToTry .= "\\".$classToTry;
                $classesToTry[] =  $classToTry;
                $fullClassesToTry[] =  $fullClassToTry;
            }
        }

        $classesToTry = array_reverse($classesToTry);
        $fullClassesToTry = array_reverse($fullClassesToTry);

        foreach ($fullClassesToTry as $classId => $classToTry) {
            if (self::checkController($classToTry)){

                $potentialView = $classesToTry[$classId - 1] ?? false;

                if ($potentialView) {
                    if (self::checkView($classToTry, $potentialView)) {
                        $this->function = "view_" . $potentialView;
                        $this->viewfile = StringFunctions::switchToForwardSlashes($classToTry) . "/" . $potentialView;
                        $this->parameters = array_slice($parameters, sizeof($parameters) - $classId + 1);
                        $this->parentControllers = array_slice($fullClassesToTry, $classId + 1);
                        return true;
                    }
                }

                if (self::checkView($classToTry, 'main')) {
                    $this->function = "view_main";
                    $this->viewfile = StringFunctions::switchToForwardSlashes($classToTry)."/"."main";
                    $this->parameters = array_slice($parameters, sizeof($parameters) - $classId);
                    $this->parentControllers = array_slice($fullClassesToTry, $classId + 1);
                    return true;
                }
            }
        }

        $this->dieToError(404);
    }

    private function checkController(String $className)
    {
        if (!$className) {
            return false;
        }

        $nsController = self::getNameSpacedController($className);
        if (class_exists($nsController)) {
            return true;

        }
        return false;
    }

    private function checkView(String $controller, String $function)
    {
        if (!$controller || !$function) {
            return false;
        }

        if (!$this->checkController($controller)) {
            return false;
        }

        $nsController = self::getNameSpacedController($controller);

        if (!$this->controller || get_class($this->controller) != $nsController) {
            $this->controller = new $nsController();
        }

        if(method_exists($this->controller, "view_".$function)){
            return true;
        }

        return false;
    }

    private function getNameSpacedController(String $className)
    {
        return "\Controller" . $className;
    }

    private function dieToError($type = 404)
    {
        $this->setPageHeaders($type);
        $this->removeOriginalLog();
        die(header('Location:  //' . SiteSetting::get('homeDomain') . '/error/' . $type));
    }

    private function removeOriginalLog()
    {
        if(!is_null($this->baseLog)) {
            $this->baseLog->remove(true);
        }
    }

    private function setPageHeaders($headerCode = 201)
    {
        http_response_code($headerCode);
    }

    private function dealWithParameter(String $parameter)
    {
        if (preg_match('/^([a-zA-Z0-9-_=]{0,120})$/',$parameter)) {
            return $parameter;
        } else {
            $this->dieToError(400);
        }
    }

    private function runView($pageData)
    {

        $this->setPageHeaders($pageData['header']['code']);

        if ($pageData['customredirect']) {
            $this->removeOriginalLog();
            $this->dieToCustomRedirect($pageData['customredirect'], $pageData['header']['code']);
        }

        if ($pageData['redirect']) {
            $this->removeOriginalLog();
            $this->dieToRedirect($pageData['redirect'], $pageData['header']['code']);
        }

        if (isset($pageData['viewfile'])) {
            if (!stristr($pageData['viewfile'], '.')) {
                $pageData['viewfile'] .= '.php';
            }

            if ((file_exists("View/Views/".$pageData['viewfile']))) {
                $viewFile = "View/Views/" . $pageData['viewfile'];
            }

        } else if (file_exists('View/Views'.$this->viewfile.'.php')) {
            $viewFile = 'View/Views'.$this->viewfile.'.php';
        }

        if ($viewFile) {
            $innerCode = View::storeRequireIntoText($viewFile, $pageData);
        } else {
            $innerCode =$pageData['response'];
        }

        if ($this->deal_with_mime_types($pageData['type'], $innerCode)) {
            return $innerCode;
        }

        $templateName = $this->work_out_template($pageData['template']);
        unset($pageData['template']); //We are done with the template too (if there was one!)

        $pageResult = $this->check_render_template($templateName, $pageData, $innerCode);

        if ($pageResult != "") {
            return $pageResult;
        } else {
            throw new EXCEPTIONCLASS("No View File",
                "You have a controller function for this page, but no view to back it up! - Needs to be called->" . $viewFile,
                "Core Error");
        }
    }

    private function dieToCustomRedirect($url, $code = 301)
    {
        $this->setPageHeaders($code);
        die(header('Location: ' . $url));
    }

    private function dieToRedirect($url, $code = 301)
    {
        $this->setPageHeaders($code);
        die(header('Location:  //' . SiteSetting::get('homeDomain') . '/' . $url));
    }

    private function deal_with_mime_types($type, &$content)
    {
        switch($type){
            case 'API':
                if (!json_decode($content)) {
                    $content = json_encode($content);
                }
                $type = 'application/json';
                break;
            case 'text':
                $type = 'text/html';
                break;
            case 'js':
                $type = 'text/javascript';
        }

        if (preg_match('/(text|font|image|model|video|application|audio)\/[a-z-0-9.+]*/', $type)) {
            $this->set_content_header($type);
            return true;
        }

        return false;
    }

    private function set_content_header(string $contenttype)
    {
        header('Content-Type: ' . $contenttype . '; charset=UTF-8');
    }

    private function work_out_template($template)
    {
        if ($template === "") {
            return "";
        }

        return $template ?? "main";
    }

    private function check_render_template($templateName, $pageData, $renderedCode)
    {

        $templateString = "View/Templates/" . $templateName . ".php";
        if (file_exists($templateString)) {
            $pageData['existingText'] = $renderedCode;
            return View::storeRequireIntoText($templateString, $pageData);
        } else {
            return $renderedCode;
        }

    }

}
