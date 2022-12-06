<?php

namespace Metarelic\Notifications\Messages;

class DigicelMessage
{
    public string $from = '';

    public function __construct(public string $content = '') {}

    public function from($from): self
    {
        $this->from = $from;

        return $this;
    }

    public function content($content) : self
    {
        $this->content = $content;

        return $this;
    }

}