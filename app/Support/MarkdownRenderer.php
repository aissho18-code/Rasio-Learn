<?php

namespace App\Support;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownRenderer
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $environment
            ->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new TableExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    public function render(?string $markdown): string
    {
        if (blank($markdown)) {
            return '<p class="text-slate-400 italic">Belum ada konten materi.</p>';
        }

        return $this->converter
            ->convert($markdown)
            ->getContent();
    }
}