<?php

namespace Tests\Browser;

public function testRootPageWorks()
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->assertSee('Привет от Хекслета!'); // Или другой текст который есть на вашей странице
    });
}