<?php

namespace Abstracts;

interface loginAble
{
    public function requiredVars();

    public function loggedInAreaPage();

    public function onLogin();

    public function isInit();

    public function update();

    public function onLogout();

    public function loggedOutAreaPage();

    public function logout();
}
