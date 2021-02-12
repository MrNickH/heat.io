<?php

class GenericException extends Exception
{

    protected $iID;
    protected $sMessage;

    public function __construct($sCode = '', $sMessage = '', $component = '', $backTrace = [])
    {
        $sMessage = self::sanitiseSMessage($sMessage);
        /*build the exception message */
        $this->sMessage .= '<h1>Exception Thrown</h1><p><strong>' . $sCode . '</strong>' . $sMessage . '</p>';
        $aVarsAtThrow['DV'] = [];
        $aVarsAtThrow['P'] = $_POST;
        $aVarsAtThrow['G'] = $_GET;
        $aVarsAtThrow['S'] = $_SERVER;
        $aVarsAtThrow['SS'] = $_SESSION ?? array();
        $aVarsAtThrow['C'] = $_COOKIE;
        $aVarsAtThrow['F'] = $_FILES;
        $aConstants = get_defined_constants(true);
        $aVarsAtThrow['CO'] = $aConstants['user'];
        $aVarsAtThrow['HE'] = getallheaders();

        try {
            $aVarsAtThrow['B'] = @get_browser(null, true);
        } catch (Exception $e) {
            $aVarsAtThrow['B'] = 'No Browser Info Supplied.';
        }



        unset($aVarsAtThrow['HE']['Cookie']);

        /* Processing Additiona; Stack trace */
        $this->sMessage .= '<h4>Exception Build Stack Trace</h4>';
        $backTrace = $backTrace ?: debug_backtrace();

        foreach ($backTrace as $iLevel => $aLevelInfo) {
            $line = $aLevelInfo['line'] ?? '';
            $class = $aLevelInfo['class'] ?? '';
            $file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $aLevelInfo['file']) ?? '';
            
            $this->sMessage .= '#' . ($iLevel+1) . ' - ' . $file . ':' . $line . '<br/>';
            $this->sMessage .=  $class . '->' . $aLevelInfo['function'] . '() </br></br> ';
        }

        /* Processing Environment Variable */
        $this->sMessage .= '<h2>Environment variables</h2>';

        $this->addArrayInfo($aVarsAtThrow['P'], 'Get variables');
        $this->addArrayInfo($aVarsAtThrow['G'], 'Post variables');
        $this->addArrayInfo($aVarsAtThrow['S'], 'Server variables');
        $this->addArrayInfo($aVarsAtThrow['SS'], 'Session variables');
        $this->addArrayInfo($aVarsAtThrow['C'], 'Cookies');
        $this->addArrayInfo($aVarsAtThrow['F'], 'Posted Files');
        $this->addArrayInfo($aVarsAtThrow['DV'], 'Other Defined variables');
        $this->addArrayInfo($aVarsAtThrow['CO'], 'Constants');
        $this->addArrayInfo($aVarsAtThrow['HE'], 'Input headers');
        $this->addArrayInfo($aVarsAtThrow['B'], 'Browser Information');

        /*
         * Would thrown an excception if the insert failed, but yeah.
         * Add Exception into DB.
         */

        $data['code_text'] = $sCode?:'NoneProvided';
        $data['message_text'] = $this->sMessage;

        if ($component != '') {
            $data['component_text'] = $component;
        }

        $iInsertID = \CRUD::create(TABLEPREFIX . '_system_exception', $data);
        $this->iID = rand() . '-' . $iInsertID . '-' . rand();

        return $iInsertID;

    }

    private function addArrayInfo($array, $name)
    {
        $this->sMessage .= '<h3>' . $name . '</h3>';
        if ($array) {
            foreach ($array as $sArrayKey => $sArrayItem) {
                $this->sMessage .= '&nbsp;&nbsp;&nbsp;<strong>' . ucfirst($sArrayKey) . '</strong>: ';
                if (is_string($sArrayItem)) {
                    $this->sMessage .= $sArrayItem . '<br/>';
                } else {
                    $this->sMessage .= '<pre>' . var_export($sArrayItem, true) . '</pre>';
                }
            }
        } else {
            $this->sMessage .= 'No ' . $name;
        }
    }

    public static function getExceptionInfo($ID)
    {
        $WH['exception_id'] = $ID;
        $data = CRUD::one(TABLEPREFIX . '_system_exceptions', $WH);
        return $data;
    }

    public function getID()
    {
        return $this->iID;
    }

    protected function getExceptionMessage()
    {
        return $this->getFrontFacingErrorPageContent();
    }

    private function getFrontFacingErrorPageContent()
    {
        $return = \View::partialView(
            'Templates/exception',
            [
                'iID' => $this->getID(),
                'sBrowser' => getallheaders()['User-Agent'],
            ]
        );

        return $return;
    }

    private static function sanitiseSMessage(string $sMessage)
    {
        $sMessage = preg_replace('/[\n]/', '<br/>', $sMessage);
        $sMessage = preg_replace('/(#[0-9]{1,3})/', '<strong>${0}</strong>', $sMessage);
        $sMessage = str_replace('Stack trace:', '<h4>Stack Trace:</h4>', $sMessage);
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $sMessage);
    }


}
