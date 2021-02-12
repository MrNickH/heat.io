<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Linguality
 *
 * @author Nick
 */
class CORE_Linguality
{
    public static $langID = 1;


    public static function fetchPhrase($constantID)
    {

        if ($_POST['setNewLang']) {
            Linguality::$langID = $_POST['setNewLang'];
        } else {
            if ($_COOKIE['newLang']) {
                Linguality::$langID = $_COOKIE['newLang'];
            }
        }


        //Linguality::updatePhraseLocation($constantID);


        $results = $GLOBALS['DBO']->retrieve('TranslationKeys', 'PhraseConstant', $constantID, false);

        foreach ($results as $phrase) {

            if ($phrase['LanguageID'] == Linguality::$langID) {
                $phraseVal = $GLOBALS['DBO']->retrieve('Phraselation', 'ID', $phrase['PhraseID'], true);
                if ($phraseVal) {
                    if ($phraseVal['Text'] != "") {
                        return htmlspecialchars_decode($phraseVal['Text']);
                    }
                }
            }
        }

        foreach ($results as $phrase) {
            if ($phrase['LanguageID'] == 1) {
                $phraseVal = $GLOBALS['DBO']->retrieve('Phraselation', 'ID', $phrase['PhraseID'], true);
                if ($phraseVal) {
                    return htmlspecialchars_decode($phraseVal['Text']) . "<span style='font-size:8px;'>[UNTRANSLATED]</span>";
                }
            }
        }

        return "Phrase Missing.";
    }

    public static function translatePhrase($phraseConstant, $languageID, $newText)
    {

        $newText = htmlspecialchars($newText);

        $f[] = 'PhraseConstant';
        $f[] = 'LanguageID';

        $v[] = $phraseConstant;
        $v[] = $languageID;

        $phraseVal = $GLOBALS['DBO']->retrieve('TranslationKeys', $f, $v, true);
        if ($phraseVal) {
            $GLOBALS['DBO']->update('Phraselation', 'Text', $newText, 'ID', $phraseVal['PhraseID'], true);
        } else {
            $newRow['Text'] = $newText;
            $translatedPhraseID = $GLOBALS['DBO']->create('Phraselation', $newRow);

            $newTKRow['PhraseConstant'] = $phraseConstant;
            $newTKRow['LanguageID'] = $languageID;
            $newTKRow['PhraseID'] = $translatedPhraseID;

            $GLOBALS['DBO']->create('TranslationKeys', $newTKRow);
        }
    }

    public static function updatePhraseLocation($constantID)
    {

        if ($_GET['P_one'] == "index.php") {
            $page = "Home";
        } else {
            if ($_GET['P_one'] == 'favicon.ico') {
                $page = 'sitewide';
            } else {
                $page = $_GET['P_one'];
            }

        }

        $GLOBALS['DBO']->update('TranslationKeys', 'PageName', $page, 'PhraseConstant', $constantID, true);
        $GLOBALS['DBO']->update('TranslationKeys', 'SubPageName', $_GET['P_two'], 'PhraseConstant', $constantID, true);
    }
}
