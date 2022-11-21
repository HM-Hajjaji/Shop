<?php

namespace App\Message;

use App\Entity\Category;

final class TestMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     private $cat;

     public function __construct(Category $cat)
     {
         $this->cat = $cat;
     }

    public function getCat()
    {
        return $this->cat;
    }
}
