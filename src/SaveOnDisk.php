<?php

/**
 * Copyright 2021 Denys Xavier
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace DenysXavier\SSRSWrapper;

/**
 * 
 */
class SaveOnDisk implements ExportBehaviorInterface
{
    /**
     * @var resource $fileHandler
     *      Pointer to the resource opened by fopen.
     */
    private $fileHandler;

    /**
     * @var string $filename
     *      Filename or path to where the report should be saved.
     */
    private string $filename;

    /**
     * Creates a new instace of SaveOnDisk behavior
     * 
     * @param string $filename Filename or path to where the report should be saved.
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * Creates a file handler for writing on disk and adds it to the cURL configuration array.
     * 
     * @param array $options The configuration array for cURL itself.
     * 
     * @return void
     */
    public function setup(array &$options): void
    {
        $this->fileHandler = fopen($this->filename, 'w');

        $options[CURLOPT_FILE] = $this->fileHandler;
    }

    /**
     * Closes the file handler resource used by cURL.
     * 
     * @return void
     */
    public function dispose(): void
    {
        if (!is_resource($this->fileHandler)) {
            fclose($this->fileHandler);
        }
    }
}
