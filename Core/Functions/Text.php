<?php

class Text
{
    public static function buttonGen(
        ?string $link,
        ?string $font_awesome,
        ?string $text,
        ?string $class = "btn-info",
        ?string $aCustom = "",
        ?bool $button = false
    ) :string {
        return \View::partialView('Partial/common/button',
            ['link' => $link, 'class' => $class, 'fa' => $font_awesome, 'text' => $text, 'aCustom' => $aCustom, 'button' => $button]);
    }
}