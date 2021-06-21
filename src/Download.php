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
 * Behavior for the SSRSWrapper::export method. In this case, all of the report content will be dispatched directly to the user browser.
 */
class Download implements ExportBehaviorInterface
{
    /**
     * @var string $filename
     *      Name of the file to be used by the browser when the data is dispatched.
     */
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function setup(array &$options): void
    {
        $options[CURLOPT_WRITEFUNCTION] = function ($ch, $data) {
            echo $data;

            return strlen($data);
        };

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $this->filename . '"');
    }

    /**
     * Does nothing since no resource is used during download setup event.
     * 
     * @return void
     */
    public function dispose(): void
    {
    }
}
