<?php

namespace forms\core;

class FontCollection extends Collection {

    public function add($font, $key = ''){
        parent::addItem($font, $key);
    }
}