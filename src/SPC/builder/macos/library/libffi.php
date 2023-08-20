<?php

declare(strict_types=1);

namespace SPC\builder\macos\library;

use SPC\exception\FileSystemException;
use SPC\exception\RuntimeException;

class libffi extends MacOSLibraryBase
{
    public const NAME = 'libffi';

    /**
     * @throws RuntimeException
     * @throws FileSystemException
     */
    protected function build(): void
    {
        [, , $destdir] = SEPARATED_PATH;
        shell()->cd($this->source_dir)
            ->exec(
                "{$this->builder->configure_env} ./configure " .
                '--enable-static ' .
                '--disable-shared ' .
                "--host={$this->builder->getOption('arch')}-apple-darwin " .
                "--target={$this->builder->getOption('arch')}-apple-darwin " .
                '--prefix= ' // use prefix=/
            )
            ->exec('make clean')
            ->exec("make -j{$this->builder->concurrency}")
            ->exec("make install DESTDIR={$destdir}");
        $this->patchPkgconfPrefix(['libffi.pc']);
    }
}
