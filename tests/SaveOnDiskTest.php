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

namespace DenysXavier\SSRSWrapper\Tests;

use DenysXavier\SSRSWrapper\SaveOnDisk;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Report class
 */
class SaveOnDiskTest extends TestCase
{
    /**
     * @test
     */
    public function anFileHandlerMustBeSetUpOnCURLOptions()
    {
        $options = [];

        $filepath = vfsStream::setup()->url() . '/file.pdf';

        $behavior = new SaveOnDisk($filepath);
        $behavior->setup($options);

        $this->assertTrue(is_resource($options[CURLOPT_FILE]));

        $behavior->dispose();
    }
}
