<?php

namespace Openbiz\Easy\Element;

use Openbiz\Easy\Element\LabelText;

class LabelBack extends LabelText
{
    protected function getLink()
    {
        return "javascript:history.go(-1);";
    }

}
