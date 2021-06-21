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
 * Interface that standardizes an exportation behavior assuming that new options for cURL might be added and any opened resource might be disposed.
 */
interface ExportBehaviorInterface
{
    /**
     * Adds or modifies the values of the array that will be used to configure the cURL resource. This is called before cURL is executed.
     * 
     * @param array $options The configuration array for cURL itself.
     * 
     * @return void
     */
    public function setup(array &$options): void;

    /**
     * Disposes any resource created during the setup event. This is called after cURL is executed and before cURL resource itself is disposed.
     * 
     * @return void
     */
    public function dispose(): void;
}
