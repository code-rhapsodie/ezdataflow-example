<?php
declare(strict_types=1);

namespace AppBundle\DataflowType\Writer;

use CodeRhapsodie\DataflowBundle\DataflowType\Writer\WriterInterface;

class MultipleContentStructureWriter implements WriterInterface
{
    /**
     * @var WriterInterface
     */
    private $contentStructureWriter;

    public function __construct(WriterInterface $contentStructureWriter)
    {
        $this->contentStructureWriter = $contentStructureWriter;
    }

    public function prepare()
    {
        $this->contentStructureWriter->prepare();
    }

    public function write($items)
    {
        foreach ($items as $item) {
            $this->contentStructureWriter->write($item);
        }
    }

    public function finish()
    {
        $this->contentStructureWriter->finish();
    }

}
