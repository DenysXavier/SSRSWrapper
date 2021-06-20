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
class Download implements ExportBehaviorInterface
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function setup(array &$options): void
    {
        $options[CURLOPT_WRITEFUNCTION] = function ($ch, $data) {
            echo $data;

            /*ob_flush();
            flush();*/
            return strlen($data);
        };

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $this->filename . '"');
    }

    public function dispose(): void
    {
    }
}
