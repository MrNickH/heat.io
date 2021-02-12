<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author Nic
 */
class CORE_Edit
{

    var $editableVars;
    var $object;

    function __construct($obj)
    {
        if ($obj instanceof editable) {
            $this->editableVars = $obj->editableVars();
            if ($this->editableVars) {
                foreach ($this->editableVars as $var) {

                    if (!method_exists($obj, "set" . $var)) {
                        throw new Exception("Unable to Create Editable " . get_class($obj) . " is not a valid editable object.");
                    }
                }
                $this->object = $obj;
            }

        } else {
            return null;
        }


    }

    function checkSetInput($postVars)
    {
        if ($postVars['editable-' . get_class($this->object)]) {
            foreach ($postVars as $key => $val) {
                if ($key != 'editable-' . get_class($this->object)) {
                    if (method_exists($this->object, $methodName)) {
                        $result = $this->object->$methodName($val);
                        if ($result !== true) {
                            $errors .= "<br/>" . $result;
                        }
                    }
                }
            }
            if ($errors) {
                CV_EditObjects::echoErrors($errors);
            }
        }


    }

    function printForm($formArray = null, $type = 1)
    {
        if (is_array($formArray)) {
            //ONLY FIELDS IN ARRAY
            return CV_EditObjects::echoEditForm($this->object, $formArray, $type);

        } else {
            if ($formArray) {
                //ONLY FIELD AS STRING
                return CV_EditObjects::echoEditForm($this->object, $formArray, $type);

            } else {
                //ALL
                return CV_EditObjects::echoEditForm($this->object, $this->editableVars, $type);
            }
        }

    }


}
