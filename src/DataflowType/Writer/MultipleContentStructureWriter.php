<?php
declare(strict_types=1);

namespace App\DataflowType\Writer;

use CodeRhapsodie\DataflowBundle\DataflowType\Writer\WriterInterface;

class MultipleContentStructureWriter implements WriterInterface
{
    public function __construct(private readonly WriterInterface $contentStructureWriter)
    {
    }

    public function prepare(): void
    {
        $this->contentStructureWriter->prepare();
    }

    public function write($items): void
    {
        foreach ($items as $item) {
            $this->contentStructureWriter->write($item);
        }
    }

    public function finish(): void
    {
        $this->contentStructureWriter->finish();
    }

}
